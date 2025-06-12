import axios from "axios";
import { getItem } from "@/helpers/storage.js";

const api = axios.create({
  baseURL: "/api", // Esto es CRUCIAL
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

export default api;