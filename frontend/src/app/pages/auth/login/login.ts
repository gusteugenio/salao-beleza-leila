import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { Auth } from '../../../core';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterModule],
  templateUrl: './login.html',
  styleUrl: './login.css',
})
export class Login implements OnInit {
  form!: FormGroup;
  loading = false;
  error = '';

  constructor(
    private fb: FormBuilder,
    private auth: Auth,
    private router: Router,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit() {
    this.form = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]]
    });
  }

  onSubmit() {
    if (this.form.invalid) {
      this.error = 'Preencha todos os campos corretamente';
      return;
    }

    this.loading = true;
    this.error = '';

    const { email, password } = this.form.value;
    this.auth.login(email, password).subscribe({
      next: () => {
        this.loading = false;
        this.auth.user$.subscribe(user => {
          if (user?.role === 'admin') {
            this.router.navigate(['/admin/dashboard']);
          } else {
            this.router.navigate(['/client/home']);
          }
        });
      },
      error: (err) => {
        this.loading = false;
        
        // Backend validation errors
        if (err.error?.errors) {
          const errors = err.error.errors;
          if (errors.email?.[0]) {
            this.form.get('email')?.setErrors({ 'backend': errors.email[0] });
            this.form.get('email')?.markAsTouched();
          }
          if (errors.password?.[0]) {
            this.form.get('password')?.setErrors({ 'backend': errors.password[0] });
            this.form.get('password')?.markAsTouched();
          }
          this.error = 'Corrija os erros acima';
        } else {
          this.error = err.error?.message || 'Erro ao fazer login';
        }
        
        this.cdr.markForCheck();
      }
    });
  }
}

