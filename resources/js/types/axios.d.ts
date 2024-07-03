import { AxiosRequestConfig } from "../";

declare module 'axios' {
  export interface AxiosRequestConfig {
    errorHandle?: boolean;
  }
}
