import { getItem } from '@/helpers/storage';

export function apiHeaders() {
  const token = getItem('auth_token', true) || getItem('auth_token');
  // Depuración visible en consola
  if (!token) {
    console.warn("No se encontró token de autenticación.");
  } else {
    console.log("Token encontrado para autenticación:", token);
  }
  return {
    headers: {
      // Solo agrega Authorization si hay token
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
      Accept: 'application/json'
    }
  };
}