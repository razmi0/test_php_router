//@ts-check
const PORT = 8080;
const API_URL = `http://localhost:${PORT}/api/v1.0/produit`;
const API_READ_ALL_ENDPOINT = `${API_URL}/list`;
const API_READ_ONE_ENDPOINT = `${API_URL}/listone`;
const API_DELETE_ONE_ENDPOINT = `${API_URL}/delete`;
const API_CREATE_ONE_ENDPOINT = `${API_URL}/new`;
const API_UPDATE_ONE_ENDPOINT = `${API_URL}/update`;
const COMMON_HEADERS = {
  "Content-Type": "application/json",
  Accept: "application/json",
  credentials: "include",
};

export const fetchReadOne = async (clientData) => {
  const { id } = clientData;
  const response = await fetch(`${API_READ_ONE_ENDPOINT}?id=${id}`, {
    headers: COMMON_HEADERS,
  });
  const json = await response.json();
  console.log(json);
  return [json, response];
};
export const fetchReadAll = async () => {
  const response = await fetch(API_READ_ALL_ENDPOINT, {
    headers: COMMON_HEADERS,
  });
  const json = await response.json();
  return [json, response];
};
export const fetchDeleteOne = async (clientData) => {
  const fetchOptions = {
    method: "DELETE",
    body: JSON.stringify(clientData),
    headers: COMMON_HEADERS,
  };
  const response = await fetch(API_DELETE_ONE_ENDPOINT, fetchOptions);
  const json = await response.json();
  return [json, response];
};
export const fetchCreateOne = async (clientData) => {
  const fetchOptions = {
    method: "POST",
    body: JSON.stringify(clientData),
    headers: COMMON_HEADERS,
  };
  const response = await fetch(API_CREATE_ONE_ENDPOINT, fetchOptions);
  console.log(response);
  const json = await response.json();
  console.log(json);
  return [json, response];
};
export const fetchUpdateOne = async (clientData) => {
  const fetchOptions = {
    method: "PUT",
    body: JSON.stringify(clientData),
    headers: COMMON_HEADERS,
  };
  const response = await fetch(API_UPDATE_ONE_ENDPOINT, fetchOptions);
  const json = await response.json();
  return [json, response];
};
