import i18n from "i18next";
import { initReactI18next } from 'react-i18next';
import LanguageDetector from "i18next-browser-languagedetector";
import XHR from "i18next-xhr-backend";

import translationEn from "../../lang/en.json";
import translationRu from "../../lang/ru.json";
import translationCn from "../../lang/cn.json";

i18n
  .use(XHR)
  .use(LanguageDetector)
  .use(initReactI18next)
  .init({
    react: {
      useSuspense: false
    },
    debug: true,
    lng: "en",
    fallbackLng: "en", // use en if detected lng is not available

    keySeparator: false, // we do not use keys in form messages.welcome

    interpolation: {
      escapeValue: false // react already safes from xss
    },

    resources: {
      en: {
        translations: translationEn
      },
      ru: {
        translations: translationRu
      },
      cn: {
        translations: translationCn
      }
    },
    defaultNS: "translations"
  });

export default i18n;