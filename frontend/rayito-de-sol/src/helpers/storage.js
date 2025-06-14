export function setItem(key, value, useSession = false) {
  const storage = useSession ? sessionStorage : localStorage;
  storage.setItem(key, typeof value === 'string' ? value : JSON.stringify(value));
}

export function getItem(key) {
  let value = localStorage.getItem(key);
  if (value === null) value = sessionStorage.getItem(key);
  if (!value) return null;
  try {
    return JSON.parse(value);
  } catch {
    return value; // Si no es JSON, devuelve el string directamente (por ej. token)
  }
}

export function removeItem(key) {
  // Borra de ambos storages para evitar inconsistencias
  localStorage.removeItem(key);
  sessionStorage.removeItem(key);
}