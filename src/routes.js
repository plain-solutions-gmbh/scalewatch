import RoutinesView from '@/components/RoutinesView.vue'
import LoginView from '@/components/LoginView.vue'
import MaintenanceView from '@/components/MaintenanceView.vue'
import RoutineView from '@/components/RoutineView.vue'

export const routes = [
  {
    name: 'routines',
    path: '/',
    component: RoutinesView,
    meta: {
      title: 'Routines',
      icon: 'refresh',
      color: '#3399FF',
      shownav: true,
    },
  },

  {
    name: 'login',
    path: '/login',
    component: LoginView,
    meta: {
      title: 'Login',
      icon: 'group',
      shownav: false,
      fullscreen: true,
    },
  },
  {
    name: 'routine',
    path: '/routine/:index',
    component: RoutineView,
    props: true,
    meta: {
      title: 'Routine',
      shownav: false,
    },
  },
  {
    name: 'maintenance',
    path: '/maintenance',
    component: MaintenanceView,
    meta: {
      title: 'Maintenance',
      shownav: false,
      fullscreen: true,
    },
  },
]
