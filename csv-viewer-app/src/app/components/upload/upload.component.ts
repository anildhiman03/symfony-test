// src/app/components/upload/upload.component.ts
import { Component } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-upload',
  templateUrl: './upload.component.html',
  styleUrls: ['./upload.component.css'],
})
export class UploadComponent {
  csvContent: any[] = [];
  errorMessage: string = '';

  constructor(private http: HttpClient, private authService: AuthService) {}

  onFileSelected(event: any): void {
    const file: File = event.target.files[0];
    if (file) {
      const formData = new FormData();
      formData.append('file', file, file.name);

      const headers = new HttpHeaders({
        Authorization: `Bearer ${this.authService.getToken()}`,
      });

      this.http.post<any>('http://localhost:8000/api/upload', formData, { headers }).subscribe(
        (response) => {
          this.csvContent = response.data;
        },
        (error) => {
          this.errorMessage = 'File upload failed';
        }
      );
    }
  }
}
