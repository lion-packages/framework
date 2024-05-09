export default function useApiResponse() {
  const getResponseFromRules = (title, data) => {
    if (data.data["rules-error"]) {
      return Object.entries(data.data["rules-error"]).map(
        ([index, message]) => ({
          status: "error",
          title: title,
          message: message,
        })
      );
    }

    return [];
  };

  return { getResponseFromRules };
}
