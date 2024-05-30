import useApiResponse from "../../src/hooks/useApiResponse";
import { expect, test } from "vitest";

test("getResponseFromRulesIsEmptyWithEmptyObject", () => {
  const { getResponseFromRules } = useApiResponse();

  const result = getResponseFromRules("test", {});

  expect(result).toStrictEqual([]);
});

test("getResponseFromRulesIsEmptyWithNull", () => {
  const { getResponseFromRules } = useApiResponse();

  const result = getResponseFromRules("test", null);

  expect(result).toStrictEqual([]);
});

test("getResponseFromRulesIsEmptyWithDataUndefined", () => {
  const { getResponseFromRules } = useApiResponse();

  const result = getResponseFromRules("test", { data: undefined });

  expect(result).toStrictEqual([]);
});

test("getResponseFromRulesIsEmptyWithDataNull", () => {
  const { getResponseFromRules } = useApiResponse();

  const result = getResponseFromRules("test", { data: null });

  expect(result).toStrictEqual([]);
});

test("getResponseFromRulesIsEmptyWithRulesErrosUndefined", () => {
  const { getResponseFromRules } = useApiResponse();

  const result = getResponseFromRules("test", {
    data: { "rules-error": undefined },
  });

  expect(result).toStrictEqual([]);
});

test("getResponseFromRulesIsEmptyWithRulesErrosNull", () => {
  const { getResponseFromRules } = useApiResponse();

  const result = getResponseFromRules("test", {
    data: { "rules-error": null },
  });

  expect(result).toStrictEqual([]);
});

test("getResponseFromRulesWithRulesErros", () => {
  const { getResponseFromRules } = useApiResponse();

  const result = getResponseFromRules("test", {
    data: { "rules-error": { idusers: "Error message" } },
  });

  expect(result).toStrictEqual([
    { status: "error", title: "test", message: "Error message" },
  ]);
});
