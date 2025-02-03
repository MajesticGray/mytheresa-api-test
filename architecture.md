# Project Architecture and Design Decisions

## Overview

This project is a RESTful API developed using [PHP 8.4](https://www.php.net/releases/8.4/en.php), [Symfony 7.2](https://symfony.com/doc/7.2/index.html), and [API Platform](https://api-platform.com/docs/symfony/) v4.  
The API exposes a single endpoint to fetch available products, optionally filtered by category and maximum price.  

The application employs **Domain-Driven Design (DDD)** principles to ensure separation of concerns, scalability, and maintainability.

Below is an explanation of the key decisions, the architecture, and the rationale behind the structure.

---

## 1. Domain-Driven Design (DDD) Philosophy

### Why DDD?

1. **Separation of Concerns**:

   - DDD enforces a clear separation between the **Domain**, **Application**, and **Infrastructure** layers.
   - This ensures that business logic is independent of frameworks (e.g., Symfony or Doctrine) and can evolve without affecting other parts of the application.

2. **Maintainability**:

   - Business logic is encapsulated in the **Domain** layer, making the application easier to understand and extend.
   - Repositories abstract database interactions, ensuring that persistence logic does not leak into the domain.

3. **Scalability**:
   - DDD allows the application to grow by adding new services, entities, and use cases with minimal impact on existing functionality.

---

## 2. Architecture and File Structure

### High-Level Overview
The project is divided into the following layers:

1. **Domain Layer [src/Domain](src/Domain)**:

   - Contains the core business logic and models.
   - Free of any dependencies on external frameworks.

2. **Application Layer [src/Application](src/Application)**:
   
   - Orchestrates application use cases and data transformations (e.g., DTOs).
   - Acts as the intermediary between the domain and infrastructure layers.

3. **Infrastructure Layer [src/Infrastructure](src/Infrastructure)**:

   - Handles database interactions, state providers, factories, and framework-specific logic (e.g., Symfony, Doctrine).

---

## 3. Key Components and Their Purpose

### 3.1 API Platform

**Why API Platform?**

- Auto-generates RESTful and GraphQL endpoints from entities or DTOs (application/vnd.api+json format has been chosen).
- Provides features like pagination, filtering, and validation out of the box (although custom implementations have been developed to acomplish a fully DDD implementation).
- Accelerates development by abstracting boilerplate code for API creation.

**Integration**:

The [ProductDtoProvider](src/Infrastructure/Symfony/StateProvider/ProductDtoProvider.php) in [src/Infrastructure/Symfony/StateProvider](src/Infrastructure/Symfony/StateProvider) implements custom logic to fetch project data, ensuring it adheres to the DDD principle of delegating API-specific concerns to the infrastructure layer.  
It is responsible for fetching, transforming, and returning DTOs (Data Transfer Objects) instead of exposing Doctrine entities directly to the API.

Key Responsibilities of ProductDtoProvider:

Fetching Data from the Database (via Doctrine Repositories)

- Retrieves product entities using the repository pattern.
- Ensures that only necessary queries are executed, optimizing performance.

Applying Business Rules & Filters

- Applies domain logic before returning data.
- Ensures that API consumers only receive relevant products.
- Can enforce access control or filter data based on certain conditions.

Transforming Entities into DTOs

- Converts Doctrine entities into lightweight DTOs optimized for API responses.
- Ensures decoupling between the domain model and the API response.
- Helps prevent overexposure of internal data.

Integrating with ApiPlatform

- Serves as the custom provider for the Product resource.
- Controls the data transformation layer before exposing it through the API.

---

### 3.2 Discount Calculators

There are two different discount calculators, tagged as `mytheresa.discount.calculator`.  
This allows the underlying system to easily identify them and perform bulk operations.

**CategoryDiscountCalculator**:

- The [CategoryDiscountCalculator](src/Domain/Service/DiscountCalculator/CategoryDiscountCalculator.php) applies discounts to all products in a category.
- Implements the [DiscountCalculatorInterface](src/Domain/Service/DiscountCalculator/DiscountCalculatorInterface.php).

**IndividualDiscountCalculator**:

- The [IndividualDiscountCalculator](src/Domain/Service/DiscountCalculator/IndividualDiscountCalculator.php) applies discounts to individual products.
- Implements the [DiscountCalculatorInterface](src/Domain/Service/DiscountCalculator/DiscountCalculatorInterface.php).


**Why Interfaces?**

- The [DiscountCalculatorInterface](src/Domain/Service/DiscountCalculator/DiscountCalculatorInterface.php) ensures a consistent contract for all discount calculators.
- Enables extensibility: new discount types can be added without modifying existing logic.

---

### 3.3 BestDiscountCalculatorStrategy

**Purpose**:

- The [BestDiscountCalculatorStrategy](src/Domain/Service/BestDiscountCalculatorStrategy.php) service determines the best discount for a product when multiple discounts are applicable.  
Each discount type is handled by an custom service, and this one determines which one is to be chosen.
- Adheres to the **Single Responsibility Principle** by separating discount selection logic from the discount calculation itself.

**Why a Strategy?**

- Encapsulates the logic for selecting the best discount.
- Makes the solution modular and easy to test.

---

### 3.4 ProductPriceCalculator

**Purpose**:

- The [ProductPriceCalculator](src/Domain/Service/ProductPriceCalculator.php) service coordinates the discount calculation process and returns a [ProductPrice](src/Domain/Model/ProductPrice.php) domain object, containing:

  - Original price (an integer).
  - Final price after discount (an integer).
  - Discount percentage (an integer, null, if none).
  - Currency (a string represeting the currency).

**Why a Dedicated Service?**

- Centralizes pricing logic, ensuring consistency across the application.
- Adheres to DDD principles by encapsulating this logic in the domain layer.

---

### 3.5 DTOs

**Purpose**:

The [DTOs](src/Application/Dto/) transform domain data into a format suitable for API responses.

- [ProductDto](src/Application/Dto/ProductDto.php) includes the relevant data about a product, including its sku, name, category and price (which is itself another DTO).
- [ProductPriceDto](src/Application/Dto/ProductPriceDto.php) includes computed price details, like the original and final price, the discount and the currency.

**Why DTOs?**

- Ensures that the API response structure is independent of the domain model.
- Prevents domain objects from being exposed directly, maintaining a clean separation between layers.

---

### 3.6 Repositories

**Purpose**:

- The [ProductRepository](src/Infrastructure/Doctrine/Repository/ProductRepository.php) in the infrastructure layer, defines methods for retrieving products with optional filtering, using Doctrine to query the database matching criteria.

**Why Repositories?**

- Abstract the persistence logic, adhering to the **Repository Pattern**.
- Makes the domain layer independent of the persistence mechanism.

---

### 3.7 Factories

**Purpose**:

- Domain factories ([ProductFactory](src/Domain/Factory/ProductFactory.php), [ProductCategoryFactory](src/Domain/Factory/ProductCategoryFactory.php)) create domain objects for testing, while ensuring domain invariants are respected.
- Symfony factories ([ProductFactory](src/Infrastructure/Symfony/Factory/ProductFactory.php), [ProductCategoryFactory](src/Infrastructure/Symfony/Factory/ProductCategoryFactory.php)) handle test data generation and ephemeral database persistence using [Foundry](https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html).

---

### 3.8 State Provider

**Purpose**:

- [ProductDtoProvider](src/Infrastructure/Symfony/StateProvider/ProductDtoProvider.php) provides a custom data provider for API Platform, fetching data based on filters (e.g., category, max price). Also handles pagination.

**Why a State Provider?**

- Decouples API-specific logic from the domain and application layers.
- Ensures the endpoint adheres to the DDD principle of delegating API logic to the infrastructure layer.

---

## 4. Decision Rationale

### 4.1 DDD Structure

- Separating the domain, application, and infrastructure layers ensures a clean and modular codebase.
- Business logic is encapsulated in the domain layer, reducing coupling with external libraries.

### 4.2 Service-Oriented Design

- Discount calculators, strategies, and the price calculator are implemented as services to promote reusability and testability.
- The [DiscountCalculatorInterface](src/Domain/Service/DiscountCalculator/DiscountCalculatorInterface.php) enables open/closed principle (adding new discount types without modifying existing ones).

### 4.3 API Platform for Rapid Development

- API Platform automates much of the REST API development process, saving time while adhering to best practices.
- Pagination, filtering, and serialization are handled efficiently without custom implementations.

---

## 5. Summary of Key Benefits

1. **Scalability**:

   - The modular design allows for easy extension (e.g., adding new discount types or features).

2. **Testability**:

   - Each service and layer is independently testable, adhering to the single responsibility principle.

3. **Maintainability**:

   - A clean separation of concerns ensures that changes in one part of the system do not ripple through others.

4. **Efficiency**:

   - API Platform accelerates development by automating repetitive tasks while maintaining flexibility.

## 6. Included Test Files

There are 7 indivudal tests included in 5 files, separated into **API tests**, which test the API by performing HTTP calls, and **Domain tests** which test the business logic.

Run all tests by executing `docker exec -it mytheresa_backend_php bin/phpunit` from the guest machine.
Optionally, run the tests included in one of the previous category by filtering by group:

- `docker exec -it mytheresa_backend_php bin/phpunit --group=api`
- `docker exec -it mytheresa_backend_php bin/phpunit --group=domain`

The tests use a custom database, `mytheresa_test`, which is populated with sample data and reset after each test.

> **NOTE:** if running `bin/phpunit` raises the message **'There is already an active transaction'**, please, run it again and it will work.  
> This may happen only the first time the tests are run, and it is caused by how Foundry resets and initialises the test database.
> I am currently looking into this issue.

### 6.1 API Tests

#### 1. [ProductCategoryFilterTest](tests/Api/ProductCategoryFilterTest.php)

**[testProductCategory](tests/Api/ProductCategoryFilterTest.php#L24)**:

- Verifies that the API correctly filters products by category.

#### 2. [ProductsTest](tests/Api/ProductsTest.php)

**[testGetProducts](tests/Api/ProductsTest.php#L23)**:

- Tests the retrieval of all products and HTTP response code via the API endpoint.

**[testMaxProductsReturned](tests/Api/ProductsTest.php#L46)**:

- Ensures that the API respects the maximum limit on the number of products returned in a single response.

#### 3. [ProductPriceFilterTest](tests/Api/ProductPriceFilterTest.php)

**[testProductPrice](tests/Api/ProductPriceFilterTest.php#L23)**:

- Tests the filtering of products based on their maximum price through the API.

---

### 6.2 Domain Tests

#### 1. [ProductDiscoutTest](tests/Domain/ProductDiscoutTest.php)

**[testCalculateDiscountForCategory](tests/Domain/ProductDiscoutTest.php#L33)**:

- Validates that the discount is correctly calculated for products belonging to a category with an associated discount.

**[testCalculateDiscountForProduct](tests/Domain/ProductDiscoutTest.php#L45)**:

- Ensures that the discount is applied correctly for individual products with specific discounts.

#### 2. [BestDiscountStrategyTest](tests/Domain/BestDiscountStrategyTest.php)

**[testBiggerDiscountIsApplied](tests/Domain/BestDiscountStrategyTest.php#L33)**:

- Verifies that the best (largest) discount is selected and applied when multiple discounts are available for a product.
