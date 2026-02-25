/**
 * Asistente de logística: las llamadas a Gemini se hacen desde el backend (API)
 * para no exponer la API key en el frontend.
 */
import { assistantChat } from "./apiClient";

export const getLogisticsAdvice = async (query: string): Promise<string> => {
  try {
    return await assistantChat(query);
  } catch (error) {
    console.error("Assistant API Error:", error);
    return "Lo siento, estoy teniendo dificultades para conectarme. Comprueba que la API esté en marcha y que GEMINI_API_KEY esté configurada. Intenta de nuevo más tarde.";
  }
};
