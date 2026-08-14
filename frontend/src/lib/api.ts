import axios, {
  isAxiosError,
  type AxiosInstance,
  AxiosError,
  type AxiosResponse,
  type InternalAxiosRequestConfig,
} from "axios";
import {
  clearSsoSession,
  getAccessToken,
  redirectToWebsiteLogin,
  SSO_ROLE_KEY,
} from "@/lib/sso";

// Extend AxiosInstance type to include isAxiosError and getApiErrorMessage
interface ExtendedAxiosInstance extends AxiosInstance {
  isAxiosError: typeof isAxiosError;
  getApiErrorMessage: (error: unknown, defaultMessage?: string) => string;
  getApiErrorMessageAsync: (error: unknown, defaultMessage?: string) => Promise<string>;
}

// Environment-based API URL configuration
const getApiUrl = (): string => {
  if (process.env.NEXT_PUBLIC_API_URL) {
    return process.env.NEXT_PUBLIC_API_URL;
  }
  return "http://localhost:8000/api/capstone";
};

const api = axios.create({
  baseURL: getApiUrl(),
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
  withCredentials: false,
}) as ExtendedAxiosInstance;

type CachedGet = {
  expiresAt: number;
  response: AxiosResponse;
};

const responseCache = new Map<string, CachedGet>();
const inFlightGets = new Map<string, Promise<AxiosResponse>>();
let cacheGeneration = 0;
const getCacheTtl = Math.max(
  0,
  Number(process.env.NEXT_PUBLIC_API_CACHE_TTL_MS ?? 30_000),
);
const maxCacheEntries = 200;

const stableSerialize = (value: unknown): string => {
  if (value === null || typeof value !== "object") {
    return JSON.stringify(value) ?? "undefined";
  }
  if (Array.isArray(value)) return `[${value.map(stableSerialize).join(",")}]`;

  return `{${Object.entries(value as Record<string, unknown>)
    .sort(([left], [right]) => left.localeCompare(right))
    .map(([key, item]) => `${JSON.stringify(key)}:${stableSerialize(item)}`)
    .join(",")}}`;
};

const getRequestCacheKey = (config: InternalAxiosRequestConfig): string => {
  const token = getAccessToken();
  const role = typeof window !== "undefined"
    ? window.sessionStorage.getItem(SSO_ROLE_KEY)
    : null;

  return [
    token?.slice(-16) ?? "anonymous",
    role ?? "default",
    config.baseURL ?? "",
    config.url ?? "",
    stableSerialize(config.params ?? null),
  ].join("|");
};

const canCacheGet = (config: InternalAxiosRequestConfig): boolean => {
  const cacheControl = String(config.headers?.get?.("Cache-Control") ?? "").toLowerCase();

  return typeof window !== "undefined"
    && config.method?.toLowerCase() === "get"
    && !["blob", "arraybuffer", "stream"].includes(config.responseType ?? "")
    && !cacheControl.includes("no-cache")
    && !cacheControl.includes("no-store")
    && !config.url?.includes("_fresh=");
};

const networkAdapter = axios.getAdapter(api.defaults.adapter);

api.defaults.adapter = async (config) => {
  if (!canCacheGet(config)) {
    return networkAdapter(config);
  }

  const key = getRequestCacheKey(config);
  const cached = responseCache.get(key);

  if (cached && cached.expiresAt > Date.now()) {
    // Refresh insertion order so the bounded Map behaves like a small LRU.
    responseCache.delete(key);
    responseCache.set(key, cached);
    return { ...cached.response, config };
  }

  responseCache.delete(key);
  const pending = inFlightGets.get(key);
  if (pending) {
    return pending.then((response) => ({ ...response, config }));
  }

  const requestGeneration = cacheGeneration;
  const request = networkAdapter(config)
    .then((response) => {
      if (
        response.status >= 200
        && response.status < 300
        && getCacheTtl > 0
        && requestGeneration === cacheGeneration
      ) {
        responseCache.set(key, {
          expiresAt: Date.now() + getCacheTtl,
          response,
        });

        while (responseCache.size > maxCacheEntries) {
          const oldestKey = responseCache.keys().next().value;
          if (oldestKey === undefined) break;
          responseCache.delete(oldestKey);
        }
      }

      return response;
    })
    .finally(() => {
      inFlightGets.delete(key);
    });

  inFlightGets.set(key, request);
  return request;
};

export function clearApiResponseCache(): void {
  cacheGeneration += 1;
  responseCache.clear();
  inFlightGets.clear();
}

api.interceptors.request.use((config) => {
  const token = getAccessToken();
  const role = typeof window !== "undefined"
    ? window.sessionStorage.getItem(SSO_ROLE_KEY)
    : null;

  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  if (role) {
    config.headers["X-Capstone-Role"] = role;
  }

  return config;
});

api.interceptors.response.use(
  (response) => {
    if (response.config.method?.toLowerCase() !== "get") {
      clearApiResponseCache();
    }

    return response;
  },
  (error: unknown) => {
    if (
      typeof window !== "undefined" &&
      isAxiosError<{
        code?: string;
        redirect_url?: string;
      }>(error) &&
      error.response?.data?.code === "SICATA_STUDENT_NOT_REGISTERED" &&
      error.response.data.redirect_url
    ) {
      clearApiResponseCache();
      clearSsoSession();
      window.location.replace(error.response.data.redirect_url);
    }

    if (
      typeof window !== "undefined" &&
      isAxiosError(error) &&
      [401, 419].includes(error.response?.status ?? 0) &&
      !error.config?.url?.includes("/auth/exchange")
    ) {
      clearApiResponseCache();
      clearSsoSession();
      redirectToWebsiteLogin();
    }

    return Promise.reject(error);
  },
);

// Add isAxiosError helper to api object
api.isAxiosError = isAxiosError;

// Helper to extract error message from API error
api.getApiErrorMessage = (error: unknown, defaultMessage = 'An error occurred'): string => {
  if (isAxiosError(error)) {
    const axiosError = error as AxiosError<{ message?: string }>;
    const responseData = axiosError.response?.data;

    // Blob error responses (e.g. failed downloads) need special handling
    if (responseData instanceof Blob && responseData.type?.includes('application/json')) {
      // Reading a Blob is asynchronous; callers that need its message should use
      // getApiErrorMessageAsync instead.
      return defaultMessage;
    }

    return axiosError.response?.data?.message || axiosError.message || defaultMessage;
  }
  return defaultMessage;
};

// Async helper to extract error message from blob error responses
api.getApiErrorMessageAsync = async (error: unknown, defaultMessage = 'An error occurred'): Promise<string> => {
  if (isAxiosError(error)) {
    const axiosError = error as AxiosError<{ message?: string }>;
    const responseData = axiosError.response?.data;

    if (responseData instanceof Blob && responseData.type?.includes('application/json')) {
      try {
        const text = await responseData.text();
        const parsed = JSON.parse(text);
        return parsed.message || defaultMessage;
      } catch {
        return defaultMessage;
      }
    }

    return axiosError.response?.data?.message || axiosError.message || defaultMessage;
  }
  return defaultMessage;
};

export default api;
