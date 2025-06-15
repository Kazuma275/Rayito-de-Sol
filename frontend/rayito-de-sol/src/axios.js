import axios from "axios";
import { getItem, removeItem } from "@/helpers/storage.js";

const api = axios.create({
  baseURL: "http://localhost:8000/api", 
  headers: {
    "Accept": "application/json",
    "Content-Type": "application/json"
  }
});

// Interceptor para el token
api.interceptors.request.use(config => {
  const token = getItem("auth_token");
  if (token) config.headers.Authorization = `Bearer ${token}`;
  return config;
});

// Interceptor para respuestas (validación de sesión)
api.interceptors.response.use(
  response => response,
  error => {
    console.log("Interceptor de respuesta activado", error.response?.status);
    if (error.response && error.response.status === 401) {
      removeItem("auth_token");
      window.location = "/login";
    }
    return Promise.reject(error);
  }
);

export default api;