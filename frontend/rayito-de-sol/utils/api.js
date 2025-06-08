import { getItem } from '@/helpers/storage';

export function apiHeaders() {
  const token = getItem('auth_token', true) || getItem('auth_token');
  return {
    headers: {
      Authorization: `Bearer ${token}`,
      Accept: 'application/json'
    }
  }
}