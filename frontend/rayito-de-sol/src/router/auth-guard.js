import { getItem } from "@/helpers/storage";
import { reactive } from 'vue';

// Estado global de autenticación
export const authState = reactive({
  isAuthenticated: false,
  user: null,
  loading: true,
  error: null,
  redirectInProgress: false // Nueva propiedad para controlar redirecciones
});

// Función para verificar si el usuario está autenticado
export function checkAuth() {
  const token = getItem("auth_token", true) || getItem("auth_token");
  
  if (!token) {
    return false;
  }
  
  // Verificar si el token ha expirado
  const expirationTime = parseInt(localStorage.getItem('token_expiration') || '0');
  if (expirationTime && Date.now() > expirationTime) {
    // Token expirado, limpiar datos
    localStorage.removeItem('auth_token');
    localStorage.removeItem('auth_user');
    localStorage.removeItem('token_expiration');
    return false;
  }
  
  return true;
}

// Middleware de autenticación para rutas
export function authGuard(to, from, next) {
  // Si ya hay una redirección en progreso, permitir la navegación
  if (authState.redirectInProgress) {
    authState.redirectInProgress = false;
    next();
    return;
  }
  
  // Actualiza el estado de autenticación
  authState.isAuthenticated = checkAuth();
  
  // Si la ruta requiere autenticación y el usuario no está autenticado
  if (to.meta.requiresAuth && !authState.isAuthenticated) {
    // Evitar bucles: si venimos del login, no redirigir de nuevo
    if (from.path.includes('/login')) {
      next(false); // Cancelar navegación
      return;
    }
    
    // Guarda la URL a la que intentaba acceder para redirigir después del login
    localStorage.setItem('redirectAfterLogin', to.fullPath);
    
    // Determinar a qué login redirigir basado en la ruta
    const loginRoute = to.path.startsWith('/renters') ? '/renters/login' : '/login';
    
    // Añadir parámetro de sesión expirada si viene de una sesión expirada
    const sessionExpired = from.query.session_expired === 'true';
    const query = sessionExpired ? { session_expired: 'true' } : {};
    
    // Marcar que hay una redirección en progreso
    authState.redirectInProgress = true;
    
    // Redirige al login
    next({ 
      path: loginRoute,
      query: query
    });
  } else {
    // Continúa normalmente
    next();
  }
}

// Hook composable para usar en componentes
export function useAuth() {
  const redirectToLogin = () => {
    // Si ya hay una redirección en progreso, no hacer nada
    if (authState.redirectInProgress) {
      return;
    }
    
    // Determinar a qué login redirigir basado en la ruta actual
    const isRentersRoute = window.location.pathname.includes('/renters');
    const loginPath = isRentersRoute ? '/renters/login' : '/login';
    
    // Guarda la URL actual
    localStorage.setItem('redirectAfterLogin', window.location.pathname);
    
    // Marcar que hay una redirección en progreso
    authState.redirectInProgress = true;
    
    // Redirige al login
    window.location.href = loginPath + '?session_expired=true';
  };

  const requireAuth = () => {
    if (!checkAuth()) {
      // Solo redirigir si no hay una redirección en progreso
      if (!authState.redirectInProgress) {
        redirectToLogin();
      }
      return false;
    }
    return true;
  };

  return {
    isAuthenticated: checkAuth(),
    requireAuth,
    redirectToLogin,
    authState
  };
}

// Función para configurar el temporizador de expiración global
export function setupGlobalExpirationCheck() {
  // Verificar expiración cada 30 segundos
  setInterval(() => {
    if (authState.isAuthenticated && !authState.redirectInProgress) {
      const isStillValid = checkAuth();
      if (!isStillValid) {
        authState.isAuthenticated = false;
        authState.user = null;
        
        // Determinar a qué login redirigir basado en la ruta actual
        const isRentersRoute = window.location.pathname.includes('/renters');
        const loginPath = isRentersRoute ? '/renters/login' : '/login';
        
        // Marcar que hay una redirección en progreso
        authState.redirectInProgress = true;
        
        // Redirigir al login con parámetro de sesión expirada
        window.location.href = loginPath + '?session_expired=true';
      }
    }
  }, 30000); // Verificar cada 30 segundos
}