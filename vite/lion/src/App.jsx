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
import ProfileGeneral from "./pages/account/profile/components/ProfileGeneral";
import ProfileInformation from "./pages/account/profile/components/ProfileInformation";
import { ProfileProvider } from "./context/account/ProfileProvider";
import ProfileChangePassword from "./pages/account/profile/components/ProfileChangePassword";
import UsersIndex from "./pages/site-administration/users/UsersIndex";
import { UsersProvider } from "./context/site-administration/UsersProvider";
import UsersUpdate from "./pages/site-administration/users/components/UsersUpdate";
import RolesMiddleware from "./middleware/RolesMiddleware.jsx";

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

        <Route
          path="account"
          element={
            <AuthenticatedMiddleware>
              <ProfileProvider>
                <ProfileIndex />
              </ProfileProvider>
            </AuthenticatedMiddleware>
          }
        >
          <Route path="general" element={<ProfileGeneral />} />
          <Route path="information" element={<ProfileInformation />} />
          <Route path="change-password" element={<ProfileChangePassword />} />
        </Route>

        <Route path="site-administration">
          <Route
            path="users"
            element={
              <AuthenticatedMiddleware>
                <RolesMiddleware roles={["1"]}>
                  <UsersProvider>
                    <UsersIndex />
                  </UsersProvider>
                </RolesMiddleware>
              </AuthenticatedMiddleware>
            }
          />

          <Route path="users/:idusers" element={<UsersUpdate />} />
        </Route>
      </Routes>
    </Fragment>
  );
}

export default App;
