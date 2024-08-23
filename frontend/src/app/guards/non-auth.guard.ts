import { CanActivateFn } from '@angular/router';

export const NonAuthGuard: CanActivateFn = (route, state) => {
  return true;
};
