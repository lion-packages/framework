import { expect, beforeEach, test } from "vitest";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { useContext } from "react";
import {
  ResponseContext,
  ResponseProvider,
} from "../../src/context/ResponseContext";

const TestComponent = () => {
  const { toasts, addToast, removeToast } = useContext(ResponseContext);

  return (
    <div>
      <button
        onClick={() =>
          addToast([
            {
              title: "Test Toast",
              message: "This is a test",
              status: "success",
            },
          ])
        }
      >
        Add Toast
      </button>

      {toasts.map((toast) => (
        <div key={toast.id}>
          <p>{toast.title}</p>

          <button onClick={() => removeToast(toast.id)}>Remove</button>
        </div>
      ))}
    </div>
  );
};

beforeEach(() => {
  render(
    <ResponseProvider>
      <TestComponent />
    </ResponseProvider>
  );
});

test("ResponseProviderNull", () => {
  const toasts = screen.queryByText("Test Toast");

  expect(toasts).toBeNull();
});

test("ResponseProviderAddToast", async () => {
  const user = userEvent.setup();

  await user.click(screen.getByText("Add Toast"));

  const toastTitle = screen.getByText("Test Toast");

  expect(toastTitle).toBeInTheDocument();
});

test("ResponseProviderRemoveToast", async () => {
  const user = userEvent.setup();

  await user.click(screen.getByText("Add Toast"));

  const toastTitle = screen.getByText("Test Toast");

  expect(toastTitle).toBeInTheDocument();

  await user.click(screen.getByText("Remove"));

  const removedToast = screen.queryByText("Test Toast");

  expect(removedToast).toBeNull();
});
