import { Component, OnDestroy, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent implements OnInit, OnDestroy {
  public loginValid = true;
  public username: string = '';
  public password: string = '';
  public errorMessage: string = '';

  constructor(
    private authService: AuthService, private router: Router
  ) { }

  onSubmit(): void {
    this.authService.login(this.username, this.password).subscribe(
      (response) => {
        this.authService.setToken(response.token);
        this.router.navigate(['/upload']);
      },
      (error) => {
        this.errorMessage = 'Invalid login credentials';
      }
    );
  }

  public ngOnInit(): void {
    // this._authService.isAuthenticated$.pipe(
    //   filter((isAuthenticated: boolean) => isAuthenticated),
    //   takeUntil(this._destroySub$)
    // ).subscribe( _ => this._router.navigateByUrl(this.returnUrl));
  }

  public ngOnDestroy(): void {
    // this._destroySub$.next();
  }
}
