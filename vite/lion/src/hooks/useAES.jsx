import cryptoJs from "crypto-js";

export default function useAES() {
  const encode = (value) => {
    return cryptoJs.AES.encrypt(
      value,
      cryptoJs.enc.Hex.parse(import.meta.env.VITE_AES_PASSPHRASE),
      {
        iv: cryptoJs.enc.Hex.parse(import.meta.env.VITE_AES_IV),
        mode: cryptoJs.mode.CBC,
        padding: cryptoJs.pad.Pkcs7,
      }
    ).toString();
  };

  const decode = (value) => {
    return cryptoJs.AES.decrypt(
      { ciphertext: cryptoJs.enc.Base64.parse(value) },
      cryptoJs.enc.Hex.parse(import.meta.env.VITE_AES_PASSPHRASE),
      {
        iv: cryptoJs.enc.Hex.parse(import.meta.env.VITE_AES_IV),
        mode: cryptoJs.mode.CBC,
        padding: cryptoJs.pad.Pkcs7,
      }
    ).toString(cryptoJs.enc.Utf8);
  };

  return { encode, decode };
}
