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
import RentersFavorites from "@/components/pages/RentersFavorites.vue"
import RentersMessages from "@/components/pages/RentersMessages.vue"
import RentersProfile from "@/components/pages/RentersProfile.vue"
import RentersSettings from "@/components/pages/RentersSettings.vue"

const routes = [
  {
    path: "/",
    name: "PortalLanding",
    component: PortalLanding,
  },
  {
    path: "/main",
    name: "MainPage",
    component: MainPage,
  },
  {
    path: "/login",
    name: "Form",
    component: LoginForm,
  },
  {
    path: "/register",
    name: "Register",
    component: RegisterForm,
  },
  {
    path: "/booking-test",
    name: "BookingTest",
    component: BookingTest,
  },
  {
    path: "/terms",
    name: "Terms",
    component: TermsConditions,
  },
  {
    path: "/help",
    name: "Help",
    component: HelpSupportSection,
  },
  // Portal de inquilinos (login individual)
  {
    path: "/renters/login",
    name: "RentersLogin",
    component: RentersLogin,
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
      },
      {
        path: "search",
        name: "RentersSearch",
        component: RentersSearch,
      },
      {
        path: "bookings",
        name: "RentersBookings",
        component: RentersBookings,
      },
      {
        path: "favorites",
        name: "RentersFavorites",
        component: RentersFavorites,
      },
      {
        path: "messages",
        name: "RentersMessages",
        component: RentersMessages,
      },
      {
        path: "profile",
        name: "RentersProfile",
        component: RentersProfile,
      },
      {
        path: "settings",
        name: "RentersSettings",
        component: RentersSettings,
      },
    ],
  },
  // Rutas del portal de propietarios
  {
    path: "/manage",
    component: PropertyManagement,
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

export default router