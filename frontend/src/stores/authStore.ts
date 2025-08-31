// stores/authStore.ts
import { defineStore } from 'pinia'
import api from '@/services/api'

export type User = {
  id: number
  name: string
  email: string
  role: string
  created_at: string
  updated_at: string
}

const USER_KEY = 'user'
const TOKEN_KEY = 'auth_token'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as User | null,
    token: null as string | null,
    initialized: false,
  }),

  getters: {
    isStudent: (s) => s.user?.role === 'student',
    isAdmin:   (s) => s.user?.role === 'admin',
    isAuthed:  (s) => !!s.token,
  },

  actions: {
    async init() {
      if (this.initialized) return
      const storedUser  = localStorage.getItem(USER_KEY)
      const storedToken = localStorage.getItem(TOKEN_KEY)

      this.user  = storedUser ? (JSON.parse(storedUser) as User) : null
      this.token = storedToken ?? null

      if (this.token && !this.user) {
        try {
          const { data } = await api.get('/user')
          this.user = data as User
          localStorage.setItem(USER_KEY, JSON.stringify(this.user))
        } catch {
          this.logout()
        }
      }

      this.initialized = true
    },

    loadFromStorage() {
      const storedUser  = localStorage.getItem(USER_KEY)
      const storedToken = localStorage.getItem(TOKEN_KEY)
      this.user  = storedUser ? (JSON.parse(storedUser) as User) : null
      this.token = storedToken ?? null
    },

    setAuth(user: User, token: string) {
      this.user = user
      this.token = token
      localStorage.setItem(USER_KEY, JSON.stringify(user))
      localStorage.setItem(TOKEN_KEY, token)
      this.initialized = true
    },

    async refreshUser() {
      if (!this.token) return
      const { data } = await api.get('/user')
      this.user = data as User
      localStorage.setItem(USER_KEY, JSON.stringify(this.user))
    },

    logout() {
      this.user = null
      this.token = null
      localStorage.removeItem(USER_KEY)
      localStorage.removeItem(TOKEN_KEY)
    },
  },
})
