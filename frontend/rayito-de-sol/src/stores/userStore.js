import { defineStore } from 'pinia';

export const useUserStore = defineStore('user', {
  state: () => ({
    token: null,
    user: null,
    properties: [],
    storageType: 'localStorage' // default, solo informativo
  }),
  actions: {
    setToken(token, remember = true) {
      console.log('[userStore] setToken called:', token, 'remember:', remember);
      this.token = token;
      if (remember) {
        localStorage.setItem('auth_token', token);
        sessionStorage.removeItem('auth_token');
        this.storageType = 'localStorage';
      } else {
        sessionStorage.setItem('auth_token', token);
        localStorage.removeItem('auth_token');
        this.storageType = 'sessionStorage';
      }
    },
    setUser(user, remember = true) {
      console.log('[userStore] setUser called:', user, 'remember:', remember);
      this.user = user;
      if (remember) {
        localStorage.setItem('auth_user', JSON.stringify(user));
        sessionStorage.removeItem('auth_user');
        this.storageType = 'localStorage';
      } else {
        sessionStorage.setItem('auth_user', JSON.stringify(user));
        localStorage.removeItem('auth_user');
        this.storageType = 'sessionStorage';
      }
    },
    setProperties(properties) {
      console.log('[userStore] setProperties called:', properties);
      this.properties = properties;
      // Si quieres persistencia, puedes habilitar lo siguiente:
      // localStorage.setItem('user_properties', JSON.stringify(properties));
    },
    hydrate() {
      // Da prioridad a sessionStorage si existe, luego localStorage
      let token = sessionStorage.getItem('auth_token') || localStorage.getItem('auth_token');
      let user = sessionStorage.getItem('auth_user') || localStorage.getItem('auth_user');
      // Opcional: propiedades persistentes
      // let properties = localStorage.getItem('user_properties');
      console.log('[userStore] hydrate called. token:', token, 'user:', user);
      this.token = token || null;
      this.user = user ? JSON.parse(user) : null;
      // this.properties = properties ? JSON.parse(properties) : [];
    },
    logout() {
      console.log('[userStore] logout called');
      this.token = null;
      this.user = null;
      this.properties = [];
      localStorage.removeItem('auth_token');
      localStorage.removeItem('auth_user');
      // localStorage.removeItem('user_properties');
      sessionStorage.removeItem('auth_token');
      sessionStorage.removeItem('auth_user');
      window.location.href = '/portal/';
    }
  }
});