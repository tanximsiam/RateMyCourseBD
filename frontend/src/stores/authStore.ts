import { defineStore } from 'pinia'

export type User = {
  id: number
  name: string
  email: string
  role: string
  created_at: string
  updated_at: string
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as User | null,
    token: null as string | null,
  }),

  actions: {
    loadFromStorage() {
      const storedUser = localStorage.getItem('user')
      const storedToken = localStorage.getItem('auth_token')
      this.user = storedUser ? JSON.parse(storedUser) as User : null
      this.token = storedToken || null
    },

    setAuth(user: User, token: string) {
      this.user = user
      this.token = token
      localStorage.setItem('user', JSON.stringify(user))
      localStorage.setItem('auth_token', token)
    },

    logout() {
      this.user = null
      this.token = null
      localStorage.removeItem('user')
      localStorage.removeItem('auth_token')
    }
  }
})
