import { render, screen, fireEvent, waitFor } from "@testing-library/react";
import "@testing-library/jest-dom";
import { expect, test, vi } from "vitest";
import AlertResponse from "../../../src/components/AlertResponse";

const mockToasts = [
  {
    id: 1,
    status: "success",
    title: "Success",
    message: "This is a success message",
  },
  {
    id: 2,
    status: "error",
    title: "Error",
    message: "This is an error message",
  },
];

const mockRemoveToast = vi.fn();

vi.mock("../../../src/context/ResponseContext.jsx", () => ({
  toasts: mockToasts,
  removeToast: mockRemoveToast,
}));

test("AlertResponseCorrectly", () => {
  render(<AlertResponse />);

  expect(screen.getByText("Success")).toBeInTheDocument();
  expect(screen.getByText("This is a success message")).toBeInTheDocument();
  expect(screen.getByText("Error")).toBeInTheDocument();
  expect(screen.getByText("This is an error message")).toBeInTheDocument();
});

test("AlertResponseClose", async () => {
  render(<AlertResponse />);

  fireEvent.click(screen.getAllByRole("button")[0]);

  await waitFor(() => {
    expect(mockRemoveToast).toHaveBeenCalledWith(1);
  });
});
