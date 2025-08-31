import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import ViewReviewsPage from '../views/ReviewPageView.vue'
import ProfileView from '@/views/ProfileView.vue'
import SuggestedCourse from '@/views/SuggestedCourse.vue'
import { useAuthStore } from '@/stores/authStore'
import CourseOutlines from '@/views/CourseOutlines.vue'
import ReportedReviews from '@/views/ReportedReviews.vue'


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue'),
    },
    {
      path: '/courses/:id/reviews',
      name: 'CourseReviews',
      component: ViewReviewsPage
    },
    {
      path: '/profile',
      name: 'Profile',
      component: ProfileView
    },
    {
      path: '/suggested-courses',
      name: 'SuggestedCourses',
      component: SuggestedCourse,
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
      path: '/course-outlines',
      name: 'CourseOutlines',
      component: CourseOutlines,
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
      path: '/reported-reviews',
      name: 'ReportedReviews',
      component: ReportedReviews,
      meta: { requiresAuth: true, requiresAdmin: true }
    },

  ],
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()
  // ensure store is hydrated (from localStorage) before checking
  if (!auth.initialized) await auth.init()

  if (to.meta.requiresAuth && !auth.token) {
    return { name: 'home', query: { redirect: to.fullPath } }
  }
  if (to.meta.requiresAdmin && !auth.isAdmin) {
    return { name: 'home' }
  }
})

export default router
