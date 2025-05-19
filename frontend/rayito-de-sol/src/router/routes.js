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
import HelpSupportPage from "@/components/help/HelpSupportPage.vue"
import BookingTest from "@/components/views/BookingTest.vue"
import TermsConditions from "@/components/settings/TermsConditions.vue"
import LoginForm from "@/components/views/LoginForm.vue"
import RegisterForm from "@/components/views/RegisterForm.vue"
import RendersPortal from "@/RendersPortal.vue"

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
    component: HelpSupportPage,
  },
  {
    path: "/renters/login",
    name: "RenterPortal",
    component: RendersPortal,
  },
  // Añadir rutas adicionales para evitar errores 404
  {
    path: "/renters/dashboard",
    name: "RentersDashboard",
    component: RendersPortal,
  },
  {
    path: "/renters/messages",
    name: "RentersMessages",
    component: RendersPortal,
  },
  {
    path: "/manage/help",
    name: "ManageHelp",
    component: HelpSupportPage,
  },
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
    ],
  },
]

const router = createRouter({
  history: createWebHistory("/portal/"),
  routes,
})

export default router
