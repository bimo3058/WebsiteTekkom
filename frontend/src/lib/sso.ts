export const SSO_TOKEN_KEY = "capstone_access_token";
export const SSO_ROLE_KEY = "capstone_active_role";
export const SSO_LOGOUT_URL_KEY = "capstone_logout_url";
export const SSO_LOGOUT_CSRF_KEY = "capstone_logout_csrf";
export const SSO_USER_KEY = "capstone_user_snapshot";

export const websiteLoginUrl =
  process.env.NEXT_PUBLIC_SSO_LOGIN_URL || "http://localhost:8000/login";

export interface LogoutBridge {
  url: string;
  csrf_token: string | null;
}

export function getAccessToken(): string | null {
  if (typeof window === "undefined") return null;
  return window.sessionStorage.getItem(SSO_TOKEN_KEY);
}

export function saveSsoSession(
  token: string,
  logout?: LogoutBridge,
  userSnapshot?: unknown
): void {
  window.sessionStorage.setItem(SSO_TOKEN_KEY, token);

  if (userSnapshot && typeof userSnapshot === "object") {
    window.sessionStorage.setItem(SSO_USER_KEY, JSON.stringify(userSnapshot));
  }

  if (logout?.url) {
    window.sessionStorage.setItem(SSO_LOGOUT_URL_KEY, logout.url);
  }

  if (logout?.csrf_token) {
    window.sessionStorage.setItem(SSO_LOGOUT_CSRF_KEY, logout.csrf_token);
  }
}

export function clearSsoSession(): void {
  if (typeof window === "undefined") return;

  window.sessionStorage.removeItem(SSO_TOKEN_KEY);
  window.sessionStorage.removeItem(SSO_ROLE_KEY);
  window.sessionStorage.removeItem(SSO_LOGOUT_URL_KEY);
  window.sessionStorage.removeItem(SSO_LOGOUT_CSRF_KEY);
  window.sessionStorage.removeItem(SSO_USER_KEY);
  window.localStorage.removeItem("activeRole");
  window.localStorage.removeItem("sidebar_sections");
}

export function redirectToWebsiteLogin(): void {
  window.location.replace(websiteLoginUrl);
}

export function performFastLogout(): void {
  const token = getAccessToken();
  const logoutUrl = window.sessionStorage.getItem(SSO_LOGOUT_URL_KEY);
  const csrfToken = window.sessionStorage.getItem(SSO_LOGOUT_CSRF_KEY);

  clearSsoSession();

  if (logoutUrl && csrfToken) {
    const form = document.createElement("form");
    form.method = "POST";
    form.action = logoutUrl;
    form.style.display = "none";

    const csrfInput = document.createElement("input");
    csrfInput.type = "hidden";
    csrfInput.name = "_token";
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);

    document.body.appendChild(form);
    form.submit();
    return;
  }

  if (token) {
    void fetch(
      `${process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000/api/capstone"}/auth/logout`,
      {
        method: "POST",
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${token}`,
        },
        keepalive: true,
      }
    );
  }

  redirectToWebsiteLogin();
}
