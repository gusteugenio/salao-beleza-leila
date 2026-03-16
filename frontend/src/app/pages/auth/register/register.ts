import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { Auth } from '../../../core';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterModule],
  templateUrl: './register.html',
  styleUrl: './register.css',
})
export class Register implements OnInit {
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
      name: ['', [Validators.required, Validators.minLength(3)]],
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

    const { name, email, password } = this.form.value;
    this.auth.register(name, email, password).subscribe({
      next: () => {
        this.loading = false;
        this.router.navigate(['/auth/login']);
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
          if (errors.name?.[0]) {
            this.form.get('name')?.setErrors({ 'backend': errors.name[0] });
            this.form.get('name')?.markAsTouched();
          }
          if (errors.password?.[0]) {
            this.form.get('password')?.setErrors({ 'backend': errors.password[0] });
            this.form.get('password')?.markAsTouched();
          }
          this.error = 'Corrija os erros acima';
        } else {
          this.error = err.error?.message || 'Erro ao criar conta';
        }
        
        this.cdr.markForCheck();
      }
    });
  }
}

