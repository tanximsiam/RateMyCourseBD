<script setup lang="ts">
import { ref } from 'vue'
import Navbar from './components/NavBar.vue'
import LoginModal from './components/LoginModal.vue'
import { useAuthStore } from './stores/authStore'
import type { User } from './stores/authStore'

const showLogin = ref(false)
const auth = useAuthStore()

const handleLoginSuccess = (userData: User) => {
  const token = localStorage.getItem('auth_token')!
  auth.setAuth(userData, token)
  showLogin.value = false
}
</script>

<template>
  <div class="bg-slate-50 min-h-screen flex flex-col gap-6">
    <Navbar @open-login="showLogin = true" />
    <LoginModal
      v-if="showLogin"
      :show="showLogin"
      @close="showLogin = false"
      @success="handleLoginSuccess"
    />
    <div class="max-w-[1920px] justify-center mx-auto">
      <RouterView />
    </div>

  </div>
</template>

<!-- <style scoped>
header {
  line-height: 1.5;
  max-height: 100vh;
}

.logo {
  display: block;
  margin: 0 auto 2rem;
}

nav {
  width: 100%;
  font-size: 12px;
  text-align: center;
  margin-top: 2rem;
}

nav a.router-link-exact-active {
  color: var(--color-text);
}

nav a.router-link-exact-active:hover {
  background-color: transparent;
}

nav a {
  display: inline-block;
  padding: 0 1rem;
  border-left: 1px solid var(--color-border);
}

nav a:first-of-type {
  border: 0;
}

@media (min-width: 1024px) {
  header {
    display: flex;
    place-items: center;
    padding-right: calc(var(--section-gap) / 2);
  }

  .logo {
    margin: 0 2rem 0 0;
  }

  header .wrapper {
    display: flex;
    place-items: flex-start;
    flex-wrap: wrap;
  }

  nav {
    text-align: left;
    margin-left: -1rem;
    font-size: 1rem;

    padding: 1rem 0;
    margin-top: 1rem;
  }
}
</style> -->
