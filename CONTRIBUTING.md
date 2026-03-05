# Contributing to RyaanCMS

Thank you for helping make RyaanCMS better! 🎉

## Ways to Contribute

- 🐛 **Report bugs** — Open a GitHub issue
- 💡 **Suggest features** — Start a Discussion
- 🔧 **Fix bugs** — Submit a pull request
- 🎨 **Build marketplace items** — Upload templates/plugins
- 📖 **Improve docs** — Edit files in `/docs`

## Development Setup

```bash
git clone https://github.com/ryaancms/ryaancms.git
cd ryaancms
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Pull Request Process

1. Fork the repo and create a branch: `git checkout -b fix/your-fix`
2. Make your changes with clear commit messages
3. Add tests if applicable: `php artisan test`
4. Run code style: `./vendor/bin/pint`
5. Open a PR against the `main` branch

## Code Style

- Follow PSR-12
- Run `./vendor/bin/pint` before committing
- Write meaningful commit messages: `feat:`, `fix:`, `docs:`, `refactor:`

## Security Vulnerabilities

**Do NOT open public GitHub issues for security vulnerabilities.**
Email security@ryaancms.com instead. See [SECURITY.md](SECURITY.md).

## License

By contributing, you agree your contributions will be licensed under the MIT License.
