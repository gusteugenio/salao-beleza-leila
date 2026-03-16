import { HttpRequest, HttpInterceptorFn } from '@angular/common/http';
import { Auth } from './auth';
import { inject } from '@angular/core';

export const authInterceptor: HttpInterceptorFn = (request: HttpRequest<unknown>, next) => {
  const auth = inject(Auth);
  const token = auth.getToken();
  
  if (token) {
    request = request.clone({
      setHeaders: {
        Authorization: `Bearer ${token}`
      }
    });
  }
  
  return next(request);
};
