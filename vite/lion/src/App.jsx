import { Fragment } from "react";
import { Route, Routes } from "react-router-dom";
import LoginIndex from "./pages/login/LoginIndex";
import NavbarNavigation from "./pages/components/NavbarNavigation";
import RegisterIndex from "./pages/register/RegisterIndex";
import AlertResponse from "./pages/components/AlertResponse";
import DashboardIndex from "./pages/dashboard/DashboardIndex";
import AuthenticatedMiddleware from "./middleware/AuthenticatedMiddleware";

function App() {
  return (
    <Fragment>
      <NavbarNavigation />

      <AlertResponse />

      <Routes>
        <Route path="*" element={<LoginIndex />} />

        <Route
          path="dashboard"
          element={
            <AuthenticatedMiddleware>
              <DashboardIndex />
            </AuthenticatedMiddleware>
          }
        />

        <Route path="auth">
          <Route path="login" element={<LoginIndex />} />

          <Route path="register" element={<RegisterIndex />} />
        </Route>
      </Routes>
    </Fragment>
  );
}

export default App;
