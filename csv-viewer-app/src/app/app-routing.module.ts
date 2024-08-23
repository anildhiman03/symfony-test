// src/app/app-routing.module.ts
import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from './components/login/login.component';
import { UploadComponent } from './components/upload/upload.component';

const routes: Routes = [
  { path: '', component: LoginComponent },
  { path: 'upload', component: UploadComponent },
  { path: '**', redirectTo: '' }, // Redirect to login for unknown routes
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule],
})
export class AppRoutingModule {}
