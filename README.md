# I.T-Inventory-System
I.T Inventory system with PHP and MySQL/MariaDB

## Directory Structure
it_inventory_system\
├── index.php                   # Login and sign-up page.\
├── logout.php                  # logout and end session.\
├── dashboard.php               # User dashboard.\
├── profile.php                 # User profile management.\
├── manage_users.php            # Admin user management - users account setting like add, edit, delete and reset password.\
├── manage_computers.php        # CRUD for computers (with sorting, search, filter). Add, adit and delete computers record.\
├── manage_printers.php         # CRUD for printers (reuse `manage_computers.php` logic) Add, adit and delete printers record.\
├── manage_servers.php          # CRUD for servers (reuse `manage_computers.php` logic) Add, adit and delete servers record.\
├── manage_tablets.php          # CRUD for tablets (reuse `manage_computers.php` logic) Add, adit and delete tablets record.\
├── manage_phones.php           # CRUD for phones (reuse `manage_computers.php` logic) Add, adit and delete phones record.\
├── manage_accessories.php      # CRUD for accessories (reuse `manage_computers.php` logic) Add, adit and delete accessories record.\
├── manage_network_equipment.php     # CRUD for network equipments (reuse `manage_computers.php` logic) Add, adit and delete network equioments record.\
├── admin_import_export.php     # Admin import/export functionality. Excel format file.\
├── print_users.php             # Print page for users account.\
├── print_computers.php         # Print page for computers inventory. User can print the result from search and filter.\
├── print_printers.php          # Print page for printers inventory. User can print the result from search and filter.\
├── print_servers.php           # Print page for servers inventory. User can print the result from search and filter.\
├── print_tablets.php           # Print page for tablets inventory. User can print the result from search and filter.\
├── print_phones.php            # Print page for phones inventory. User can print the result from search and filter.\
├── print_accessorie.php        # Print page for accessories inventory. User can print the result from search and filter.\
├── print_network_equipmment.php      # Print page for network equipments inventory. User can print the result from search and filter.\
├── view_logs.php               # Activities like login, sign-up, logout, add, edit, delete and other will log. admin user able to view all activity record.\
├── includes/\ 
 |    ├── db.php                  # Database connection.\
 |    ├── auth.php                # Authentication and session management.\
 |    └── functions.php           # Utility functions (e.g., sorting).\
├── css/\
 |    └── style.css               # Stylesheet for UI.\
├── js/\
 |    └── script.js               # Optional JavaScript for interactivity.\
├── vendor/                     # Composer dependencies (e.g., PhpSpreadsheet).

CRUD stands for:\
✅ C - Create (Add new records to a database)\
✅ R - Read (Retrieve or display records from a database)\
✅ U - Update (Modify existing records in a database)\
✅ D - Delete (Remove records from a database)

When system don't have admin account detected, login page will ask for create admin account. Every user account have it own permission and session. Only admin user can access manage users page, admin allow to create, edit, delete and reset password any user account. Admin have full control for all access, user with view permission only can view the inventory record, user with add permission can create new record for inventory, user with edit permission can add, edit and delete inventory record.
