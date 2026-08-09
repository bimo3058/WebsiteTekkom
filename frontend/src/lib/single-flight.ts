export function createSingleFlightByKey<T>(
  request: (key: string) => Promise<T>
): (key: string) => Promise<T> {
  let currentKey: string | null = null;
  let currentRequest: Promise<T> | null = null;

  return (key: string) => {
    if (currentKey === key && currentRequest) {
      return currentRequest;
    }

    currentKey = key;
    currentRequest = request(key);

    return currentRequest;
  };
}
