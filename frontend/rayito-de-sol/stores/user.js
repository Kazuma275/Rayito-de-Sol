import { defineStore } from 'pinia'

export const useUserStore = defineStore('user', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('auth_user')) || null,
    token: localStorage.getItem('auth_user') || null,
  }),
  actions: {
    setUser(user) {
      this.user = user
      localStorage.setItem('auth_user', JSON.stringify(user))
    },
    setToken(token) {
      this.token = token
      localStorage.setItem('auth_token', token)
    },
    logout() {
      this.user = null
      this.token = null
      localStorage.removeItem('auth_user')
      localStorage.removeItem('auth_token')
    }
  }
})