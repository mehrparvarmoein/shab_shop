
Setup method-1:

1. Clone the repository and navigate to the project directory:
   ```bash
   git clone https://github.com/mehrparvarmoein/shab_shop.git shab_shop
   cd shab_shop
   ```

2. Install Composer dependencies:
   ```bash
   composer install
   ```

3. Create a copy of the `.env.example` file and rename it to `.env`:
   ```bash
   cp .env.example .env
   ```

4. Run the shop installation command:
   ```bash
   php artisan shop:install
   ```

Setup method-2:
Use the `deploy.sh` script.

Admin user:
- Username: admin
- Password: 12345678

