import { Fragment } from "react";
import { Route, Routes } from "react-router-dom";
import LoginIndex from "./pages/auth/login/LoginIndex";
import NavbarNavigation from "./pages/components/NavbarNavigation";
import RegisterIndex from "./pages/auth/register/RegisterIndex";
import AlertResponse from "./pages/components/AlertResponse";
import DashboardIndex from "./pages/dashboard/DashboardIndex";
import AuthenticatedMiddleware from "./middleware/AuthenticatedMiddleware";
import NotAuthenticatedMiddleware from "./middleware/NotAuthenticatedMiddleware";
import NotFound from "./pages/errors/NotFound";
import RecoveryPasswordIndex from "./pages/auth/recovery-password/RecoveryPasswordIndex";

function App() {
  return (
    <Fragment>
      <NavbarNavigation />

      <AlertResponse />

      <Routes>
        <Route path="*" element={<NotFound />} />

        <Route path="/" element={<LoginIndex />} />

        <Route
          path="dashboard"
          element={
            <AuthenticatedMiddleware>
              <DashboardIndex />
            </AuthenticatedMiddleware>
          }
        />

        <Route path="auth">
          <Route
            path="login"
            element={
              <NotAuthenticatedMiddleware>
                <LoginIndex />
              </NotAuthenticatedMiddleware>
            }
          />

          <Route
            path="register"
            element={
              <NotAuthenticatedMiddleware>
                <RegisterIndex />
              </NotAuthenticatedMiddleware>
            }
          />

          <Route
            path="recovery-password"
            element={
              <NotAuthenticatedMiddleware>
                <RecoveryPasswordIndex />
              </NotAuthenticatedMiddleware>
            }
          />
        </Route>
      </Routes>
    </Fragment>
  );
}

export default App;
