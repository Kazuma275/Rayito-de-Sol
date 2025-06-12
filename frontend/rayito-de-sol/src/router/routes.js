import { createRouter, createWebHistory } from "vue-router"
import PortalLanding from "@/PortalLanding.vue"
import MainPage from "@/MainPage.vue"
import PropertyManagement from "@/PropertyManagement.vue"
import DashboardPage from "@/components/dashboard/DashboardPage.vue"
import PropertiesPage from "@/components/properties/PropertiesPage.vue"
import BookingsPage from "@/components/bookings/BookingsPage.vue"
import CalendarPage from "@/components/calendar/CalendarPage.vue"
import MessagesPage from "@/components/messages/MessagesPage.vue"
import SettingsPage from "@/components/settings/SettingsPage.vue"
import HelpSupportSection from "@/components/help/HelpSupportSection.vue"
import BookingTest from "@/components/views/BookingTest.vue"
import TermsConditions from "@/components/settings/TermsConditions.vue"
import LoginForm from "@/components/views/LoginForm.vue"
import RegisterForm from "@/components/views/RegisterForm.vue"
import RentersPortal from "@/RentersPortal.vue"
import RentersLogin from "@/components/pages/RentersLogin.vue"
import RentersDashboard from "@/components/pages/RentersDashboard.vue"
import RentersSearch from "@/components/pages/RentersSearch.vue"
import RentersBookings from "@/components/pages/RentersBookings.vue"
import RentersFavoritesSection from "@/components/favorites/RentersFavoritesSection.vue"
import RentersMessages from "@/components/pages/RentersMessages.vue"
import RentersProfile from "@/components/pages/RentersProfile.vue"
import RentersSettings from "@/components/pages/RentersSettings.vue"
import RentersPropertyDetail from "@/components/properties/RentersPropertyDetail.vue"
import ResetPassword from "@/components/pages/ResetPassword.vue"
import { authGuard } from './auth-guard';

const routes = [
  {
    path: "/",
    name: "PortalLanding",
    component: PortalLanding,
    meta: { requiresAuth: false }
  },
  {
    path: "/main",
    name: "MainPage",
    component: MainPage,
    meta: { requiresAuth: false }
  },
  {
    path: "/login",
    name: "Login",
    component: LoginForm,
    meta: { requiresAuth: false }
  },
  {
    path: "/register",
    name: "Register",
    component: RegisterForm,
    meta: { requiresAuth: false }
  },
  {
    path: "/booking-test",
    name: "BookingTest",
    component: BookingTest,
    meta: { requiresAuth: false }
  },
  {
    path: "/terms",
    name: "Terms",
    component: TermsConditions,
    meta: { requiresAuth: false }
  },
  {
    path: "/help",
    name: "Help",
    component: HelpSupportSection,
    meta: { requiresAuth: false }
  },
  {
    path: "/reset-password",
    name: "ResetPassword",
    component: ResetPassword,
    meta: { requiresAuth: false }
  },
  // Portal de inquilinos (login individual)
  {
    path: "/renters/login",
    name: "RentersLogin",
    component: RentersLogin,
    meta: { requiresAuth: false }
  },
  // Resto del portal de inquilinos con layout
  {
    path: "/renters",
    component: RentersPortal,
    children: [
      {
        path: "",
        redirect: "/renters/dashboard",
      },
      {
        path: "dashboard",
        name: "RentersDashboard",
        component: RentersDashboard,
        meta: { requiresAuth: true }
      },
      {
        path: "search",
        name: "RentersSearch",
        component: RentersSearch,
        meta: { requiresAuth: true }
      },
      {
        path: "bookings",
        name: "RentersBookings",
        component: RentersBookings,
        meta: { requiresAuth: true }
      },
      {
        path: "favorites",
        name: "RentersFavoritesSection",
        component: RentersFavoritesSection,
        meta: { requiresAuth: true }
      },
      {
        path: "messages",
        name: "RentersMessages",
        component: RentersMessages,
        meta: { requiresAuth: true }
      },
      {
        path: "profile",
        name: "RentersProfile",
        component: RentersProfile,
        meta: { requiresAuth: true }
      },
      {
        path: "settings",
        name: "RentersSettings",
        component: RentersSettings,
        meta: { requiresAuth: true }
      },
      {
        path: "property/:id",
        name: "RentersPropertyDetail",
        component: RentersPropertyDetail,
        meta: { requiresAuth: false } // Esta puede ser pública si quieres
      },
    ],
  },
  // Rutas del portal de propietarios
  {
    path: "/manage",
    component: PropertyManagement,
    meta: { requiresAuth: true }, // Protege todo el grupo de rutas
    children: [
      {
        path: "",
        redirect: "/manage/dashboard",
      },
      {
        path: "dashboard",
        name: "Dashboard",
        component: DashboardPage,
      },
      {
        path: "properties",
        name: "Properties",
        component: PropertiesPage,
      },
      {
        path: "bookings",
        name: "Bookings",
        component: BookingsPage,
      },
      {
        path: "calendar",
        name: "Calendar",
        component: CalendarPage,
      },
      {
        path: "messages",
        name: "Messages",
        component: MessagesPage,
      },
      {
        path: "settings",
        name: "Settings",
        component: SettingsPage,
      },
      {
        path: "help",
        name: "Help",
        component: HelpSupportSection,
      },
    ],
  },
]

const router = createRouter({
  history: createWebHistory("/portal/"),
  routes,
})

// Aplicar el guardia de autenticación a todas las rutas
router.beforeEach(authGuard);

// Después del login exitoso, redirigir a la página que intentaba visitar
export function redirectAfterLogin(router) {
  const redirectPath = localStorage.getItem('redirectAfterLogin');
  if (redirectPath) {
    localStorage.removeItem('redirectAfterLogin');
    router.push(redirectPath);
  } else {
    router.push('/manage/dashboard');
  }
}

export default router