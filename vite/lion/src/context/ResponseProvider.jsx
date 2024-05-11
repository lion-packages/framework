import { createContext, useContext, useState } from "react";

const ResponseContext = createContext();

export function ResponseProvider({ children }) {
  const [toasts, setToasts] = useState([]);

  const addToast = (messages) => {
    setToasts([
      ...toasts,
      ...messages.map((message) => ({ ...message, id: Math.random() })),
    ]);
  };

  const removeToast = (id) => {
    setToasts(toasts.filter((toast) => toast.id !== id));
  };

  return (
    <ResponseContext.Provider value={{ toasts, addToast, removeToast }}>
      {children}
    </ResponseContext.Provider>
  );
}

export const useResponse = () => useContext(ResponseContext);
