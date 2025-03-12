import { createRouter, createWebHistory } from 'vue-router';
import PortalLanding from '@/PortalLanding.vue';
import MainPage from '@/MainPage.vue';
import PropertyManagement from '@/PropertyManagement.vue';
import DashboardPage from '@/components/dashboard/DashboardPage.vue';
import PropertiesPage from '@/components/properties/PropertiesPage.vue';
import BookingsPage from '@/components/bookings/BookingsPage.vue';
import CalendarPage from '@/components/calendar/CalendarPage.vue';
import MessagesPage from '@/components/messages/MessagesPage.vue';
import SettingsPage from '@/components/settings/SettingsPage.vue';
import HelpSupportPage from '@/components/help/HelpSupportPage.vue';

const routes = [
  {
    path: '/',
    name: 'PortalLanding',
    component: PortalLanding,
  },
  {
    path: '/main',
    name: 'MainPage',
    component: MainPage,
  },
  {
    path: '/manage',
    component: PropertyManagement,
    children: [
      {
        path: '',
        redirect: '/manage/dashboard'
      },
      {
        path: 'dashboard',
        name: 'Dashboard',
        component: DashboardPage,
      },
      {
        path: 'properties',
        name: 'Properties',
        component: PropertiesPage,
      },
      {
        path: 'bookings',
        name: 'Bookings',
        component: BookingsPage,
      },
      {
        path: 'calendar',
        name: 'Calendar',
        component: CalendarPage,
      },
      {
        path: 'messages',
        name: 'Messages',
        component: MessagesPage,
      },
      {
        path: 'settings',
        name: 'Settings',
        component: SettingsPage,
      },
      {
        path: 'help',
        name: 'Help',
        component: HelpSupportPage,
      }
    ]
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
