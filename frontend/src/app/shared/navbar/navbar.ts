import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { Auth } from '../../core/auth';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [CommonModule, RouterLink, RouterLinkActive],
  templateUrl: './navbar.html',
  styleUrl: './navbar.css',
})
export class Navbar implements OnInit {
  isAuthenticated = false;
  isAdmin = false;
  userName = '';

  constructor(public auth: Auth) {}

  ngOnInit(): void {
    this.auth.isAuthenticated$.subscribe(isAuth => {
      this.isAuthenticated = isAuth;
    });

    this.auth.user$.subscribe(user => {
      if (user) {
        this.isAdmin = user.role === 'admin';
        this.userName = user.name;
      }
    });
  }

  logout(): void {
    this.auth.logout();
  }
}
