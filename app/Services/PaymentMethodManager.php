<?php

namespace App\Services;

use Revolution\Ordering\Contracts\Payment\PaymentDriver;
use Revolution\Ordering\Contracts\Payment\PaymentMethodFactory;
use Illuminate\Support\Collection;

class PaymentMethodManager implements PaymentMethodFactory
{
    protected array $methods;

    public function __construct(array $methods = [])
    {
        $this->methods = $methods;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function addMethod(string $name, PaymentDriver $driver): void
    {
        $this->methods[$name] = $driver;
    }

    public function driver(string $name): PaymentDriver
    {
        if (!isset($this->methods[$name])) {
            throw new \InvalidArgumentException("Payment method [{$name}] is not defined.");
        }

        return $this->methods[$name];
    }

    public function methods(): Collection
    {
        return collect($this->methods)->mapWithKeys(function ($driver, $key) {
            return [$key => $driver instanceof PaymentDriver ? $driver->getName() : $key];
        });
    }

    public function keys(): Collection
    {
        return collect(array_keys($this->methods));
    }

    public function name(string $key): string
    {
        $driver = $this->methods[$key] ?? null;

        if ($driver instanceof PaymentDriver) {
            return $driver->getName();
        }

        return $key;
    }
}
