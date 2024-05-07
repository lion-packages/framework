import { Fragment } from "react";
import { Route, Routes } from "react-router-dom";
import LoginIndex from "./pages/login/LoginIndex";
import NavbarNavigation from "./pages/components/NavbarNavigation";
import RegisterIndex from "./pages/register/RegisterIndex";
import { AuthProvider } from "./context/AuthProvider";

function App() {
  return (
    <Fragment>
      <AuthProvider>
        <NavbarNavigation />

        <Routes>
          <Route path="*" element={<LoginIndex />} />

          <Route path="auth">
            <Route path="login" element={<LoginIndex />} />

            <Route path="register" element={<RegisterIndex />} />
          </Route>
        </Routes>
      </AuthProvider>
    </Fragment>
  );
}

export default App;
