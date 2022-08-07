# Collection

A lightweight PHP >= 8.1 library to create **immutable/readonly** and strictly typed collection of objects.

## Installation

```bash
composer require guennichi/collection
```

## Usage

```php
final class Person
{
    public function __construct(public readonly string $name) {}
}

/**
 * @extends Guennichi\Collection\Collection<Person>
 */
final class PersonCollection extends Guennichi\Collection\Collection
{
    public function __construct(Person ...$elements)
    {
        parent::__construct(...$elements);
    }
}

$persons = new PersonCollection(
    new Person('Person1'),
    new Person('Person2'),
    new Person('Person3'),
);

$persons->first()->name // Person1. Also supports autocomplete, thanks @template annotations.
$persons->contains(new Person('Person1')); // false
$persons->count(); // 3
$persons->each(static function (Person $person, int $index) {
    // Do something
});
$persons->filter(static fn (Person $person) => $person->name === 'Person3');

final class Employee
{
    public function __construct(public readonly string $name) {}
}

/**
 * @extends Guennichi\Collection\Collection<Employee>
 */
final class EmployeeCollection extends Guennichi\Collection\Collection
{
    public function __construct(Employee ...$elements)
    {
        parent::__construct(...$elements);
    }
}

$employees = $persons->filter(static fn (Person $person) => in_array($person->name, ['Person1', 'Person3']))
                     ->mapTo(EmployeeCollection::class, static fn (Person $person) => new Employee($person->name));

foreach ($employees as $employee) {
    // $employee is instanceof Employee class
}

$employees->first(); // Employee('Person1'): instance of Employee class
$employees->forAll(static fn (Employee $employee) => !empty($employee->name)); // True
$employees->sortAscBy(static fn (Employee $employee) => $employee->name); // new collection instance with [Employee('Person1'), Employee('Person3')]
$employees->sortDescBy(static fn (Employee $employee) => $employee->name); // new collection instance with [Employee('Person3'), Employee('Person1')]

json_encode($employees) // [{"name":"Person1"}, {"name":"Person3"}]
```
