Use Sandwich;
-- Creating the 'customers' table
CREATE TABLE customers (
    CustomerId INT AUTO_INCREMENT PRIMARY KEY,
    CustUsername VARCHAR(25) UNIQUE,
    Email VARCHAR(50),
    Phone VARCHAR(10),
    Password VARCHAR(255)
);

-- Creating the 'ingredients' table
CREATE TABLE ingredients (
    IngredientId INT AUTO_INCREMENT PRIMARY KEY,
    IngName VARCHAR(50),
    Price DECIMAL(5,2),
    Category VARCHAR(50),
    Availablily TINYINT(1) NOT NULL,
    name VARCHAR(255)
);

-- Creating the 'staff' table
CREATE TABLE staff (
    StaffId INT AUTO_INCREMENT PRIMARY KEY,
    StaffUsername VARCHAR(25) UNIQUE,
    Password VARCHAR(255),
    Role VARCHAR(50)
);

-- Creating the 'menu' table
CREATE TABLE menu (
    MenuItemId INT AUTO_INCREMENT PRIMARY KEY,
    ItemName VARCHAR(50) NOT NULL,
    Descr VARCHAR(250),
    Price DECIMAL(10,2) NOT NULL,
    Category VARCHAR(50),
    name VARCHAR(255),
    Size VARCHAR(10)
);

-- Creating the 'orders' table
CREATE TABLE orders (
    OrderId INT AUTO_INCREMENT PRIMARY KEY,
    CustomerId INT,
    OrderDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Amount DECIMAL(10,2) NOT NULL,
    Status ENUM('In Progress', 'Completed', 'Pending', 'Canceled') DEFAULT 'Pending',
    FOREIGN KEY (CustomerId) REFERENCES customers(CustomerId)
);

-- Creating the 'orderitems' table
CREATE TABLE orderitems (
    OrderItemId INT AUTO_INCREMENT PRIMARY KEY,
    OrderId INT,
    MenuItemId INT,
    IngredientId INT,
    Quantity INT NOT NULL,
    Price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (OrderId) REFERENCES orders(OrderId),
    FOREIGN KEY (MenuItemId) REFERENCES menu(MenuItemId),
    FOREIGN KEY (IngredientId) REFERENCES ingredients(IngredientId)
);

-- Creating the 'inventory' table
CREATE TABLE inventory (
    IngredientId INT AUTO_INCREMENT PRIMARY KEY,
    AmountInStock INT NOT NULL,
    ReorderThreshold INT NOT NULL,
    LastUpdated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (IngredientId) REFERENCES ingredients(IngredientId)
);

-- Creating the 'payments' table
CREATE TABLE payments (
    PaymentId INT AUTO_INCREMENT PRIMARY KEY,
    OrderId INT,
    PaymentType ENUM('Cash', 'Card'),
    AmountPaid DECIMAL(10,2) NOT NULL,
    PaymentDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (OrderId) REFERENCES orders(OrderId)
);

/*Menu Table*/
insert into menu (ItemName, Descr, Price, Category) 
values ('Ham & Cheese', 'Sliced ham, cheddar cheese, lettuce, tomato, and mayo on bread of choice', '7.79', 'sandwich');
insert into menu (ItemName, Descr, Price, Category) 
values ('Turkey Club', 'Sliced turkey, bacon, lettuce, tomato, and mayo on bread of choice', '8.59', 'sandwich');
insert into menu (ItemName, Descr, Price, Category) 
values ('Blt', 'Bacon, lettuce, tomato, and mayo on toasted bread of choice', '7.49', 'sandwich');
insert into menu (ItemName, Descr, Price, Category) 
values ('Italian', 'Salami, pepperoni, ham, provolone cheese, lettuce, tomato, onion, and Italian dressing on bread of choice', '10.79', 'sandwich');
insert into menu (ItemName, Descr, Price, Category) 
values ('Meatball', 'Italian meatballs, marinara sauce, and melted mozzarella on a toasted white bread', '11.99', 'sandwich');
insert into menu (ItemName, Descr, Price, Category) 
values ('Chicken Caesar Wrap', 'Grilled chicken, romaine lettuce, parmesan cheese, and Caesar dressing wrapped in a flour tortilla', '6.19', 'wrap');
insert into menu (ItemName, Descr, Price, Category)
values ('Build Your Own', 'Base sandwich where you can add any custom toppings you like', '6.69', 'sandwich');

/*Ingredient Table*/
insert into ingredients (IngName, Price, Category, Availablily) 
values ('White', '0.00', 'Bread', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Wheat', '0.00', 'Bread', False);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Sourdough', '0.00', 'Bread', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Multigrain', '0.00', 'Bread', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Wrap', '0.00', 'Bread', True);

insert into ingredients (IngName, Price, Category, Availablily) 
values ('Ham', '1.00', 'Protein', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Turkey', '1.00', 'Protein', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Chicken', '1.50', 'Protein', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Bacon', '1.00', 'Protein', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Salami', '1.00', 'Protein', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Pepperoni', '1.00', 'Protein', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Meatballs', '1.50', 'Protein', True);

insert into ingredients (IngName, Price, Category, Availablily) 
values ('Cheddar Cheese', '0.50', 'Cheese', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Mozzarella', '0.75', 'Cheese', False);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Provolone', '0.50', 'Cheese', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Swiss', '0.50', 'Cheese', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('American', '0.50', 'Cheese', False);

insert into ingredients (IngName, Price, Category, Availablily) 
values ('Lettuce', '0.00', 'Veggie', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Tomato', '0.00', 'Veggie', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Onion', '0.00', 'Veggie', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Bell Pepper', '0.00', 'Veggie', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Cucumber', '0.00', 'Veggie', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Spinach', '0.00', 'Veggie', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Avocado', '1.00', 'Veggie', False);

insert into ingredients (IngName, Price, Category, Availablily) 
values ('Mayo', '0.00', 'Condiment', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Mustard', '0.00', 'Condiment', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Honey Mustard', '0.00', 'Condiment', False);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Ketchup', '0.00', 'Condiment', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Italian Dressing', '0.00', 'Condiment', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Ceasar Dressing', '0.00', 'Condiment', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Hummus', '0.00', 'Condiment', True);
insert into ingredients (IngName, Price, Category, Availablily) 
values ('Balsamic Glaze', '1.00', 'Condiment', False);

insert into staff (StaffUsername, Password, Role)
values ('SandwichGuy', 'EatSandwich123', 'Admin');

/*Inventory Base Data*/
insert into inventory (AmountInStock, ReorderThreshold)
values ('38', '15');
insert into inventory (AmountInStock, ReorderThreshold)
values ('0', '15');
insert into inventory (AmountInStock, ReorderThreshold)
values ('24', '15');
insert into inventory (AmountInStock, ReorderThreshold)
values ('13', '15');
insert into inventory (AmountInStock, ReorderThreshold)
values ('19', '15');

insert into inventory (AmountInStock, ReorderThreshold)
values ('41', '30');
insert into inventory (AmountInStock, ReorderThreshold)
values ('33', '30');
insert into inventory (AmountInStock, ReorderThreshold)
values ('42', '30');
insert into inventory (AmountInStock, ReorderThreshold)
values ('61', '50');
insert into inventory (AmountInStock, ReorderThreshold)
values ('26', '25');
insert into inventory (AmountInStock, ReorderThreshold)
values ('3', '25');
insert into inventory (AmountInStock, ReorderThreshold)
values ('58', '40');

insert into inventory (AmountInStock, ReorderThreshold)
values ('91', '75');
insert into inventory (AmountInStock, ReorderThreshold)
values ('10', '35');
insert into inventory (AmountInStock, ReorderThreshold)
values ('82', '75');
insert into inventory (AmountInStock, ReorderThreshold)
values ('57', '50');
insert into inventory (AmountInStock, ReorderThreshold)
values ('12', '100');

insert into inventory (AmountInStock, ReorderThreshold)
values ('53', '50');
insert into inventory (AmountInStock, ReorderThreshold)
values ('38', '35');
insert into inventory (AmountInStock, ReorderThreshold)
values ('41', '30');
insert into inventory (AmountInStock, ReorderThreshold)
values ('38', '25');
insert into inventory (AmountInStock, ReorderThreshold)
values ('26', '25');
insert into inventory (AmountInStock, ReorderThreshold)
values ('37', '20');
insert into inventory (AmountInStock, ReorderThreshold)
values ('0', '15');

insert into inventory (AmountInStock, ReorderThreshold)
values ('61', '50');
insert into inventory (AmountInStock, ReorderThreshold)
values ('54', '50');
insert into inventory (AmountInStock, ReorderThreshold)
values ('2', '20');
insert into inventory (AmountInStock, ReorderThreshold)
values ('111', '100');
insert into inventory (AmountInStock, ReorderThreshold)
values ('18', '15');
insert into inventory (AmountInStock, ReorderThreshold)
values ('30', '25');
insert into inventory (AmountInStock, ReorderThreshold)
values ('16', '15');
insert into inventory (AmountInStock, ReorderThreshold)
values ('0', '10');

/*INSERT INTO Inventory (IngredientId, AmountInStock, ReorderThreshold) VALUES
(1, 100, 20), -- Example IngredientId 1
(2, 150, 30), -- Example IngredientId 2
(3, 200, 50); -- Example IngredientId 3
