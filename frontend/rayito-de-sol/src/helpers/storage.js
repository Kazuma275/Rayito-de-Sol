export function setItem(key, value, useSession = false) {
  const storage = useSession ? sessionStorage : localStorage;
  storage.setItem(key, typeof value === 'string' ? value : JSON.stringify(value));
}

export function getItem(key, useSession = false) {
  const storage = useSession ? sessionStorage : localStorage;
  const value = storage.getItem(key);
  try {
    return JSON.parse(value);
  } catch {
    return value;
  }
}

export function removeItem(key, useSession = false) {
  const storage = useSession ? sessionStorage : localStorage;
  storage.removeItem(key);
}
