import { Fragment } from "react";
import { Route, Routes } from "react-router-dom";
import LoginIndex from "./pages/auth/login/LoginIndex";
import Header from "./pages/components/Header";
import RegisterIndex from "./pages/auth/register/RegisterIndex";
import AlertResponse from "./pages/components/AlertResponse";
import DashboardIndex from "./pages/dashboard/DashboardIndex";
import AuthenticatedMiddleware from "./middleware/AuthenticatedMiddleware";
import NotAuthenticatedMiddleware from "./middleware/NotAuthenticatedMiddleware";
import NotFound from "./pages/errors/NotFound";
import RecoveryPasswordIndex from "./pages/auth/recovery-password/RecoveryPasswordIndex";
import ProfileIndex from "./pages/account/profile/ProfileIndex";

function App() {
  return (
    <Fragment>
      <Header />

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

        <Route path="account" element={<ProfileIndex />} />

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
