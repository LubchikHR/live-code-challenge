#!/usr/bin/env bash
set -euo pipefail

# 1) Пакети Behat + Mink для роботи з Symfony Kernel
composer require --dev --with-all-dependencies \
  behat/behat:^3.15 \
  friends-of-behat/symfony-extension:^2.6 \
  friends-of-behat/mink:^1.10 \
  friends-of-behat/mink-browserkit-driver:^1.5 \
  symfony/browser-kit:^7.1 \
  symfony/css-selector:^7.1

# 2) Структура тек
mkdir -p tests/Behat features

# 3) behat.yml (якщо ще нема)
if [ ! -f behat.yml ]; then
cat > behat.yml <<'YAML'
default:
  suites:
    app:
      paths: [features]
      contexts: [App\\Tests\\Behat\\FeatureContext]
  extensions:
    FriendsOfBehat\\SymfonyExtension:
      kernel:
        class: App\\Kernel
YAML
fi

# 4) FeatureContext (якщо ще нема)
if [ ! -f tests/Behat/FeatureContext.php ]; then
cat > tests/Behat/FeatureContext.php <<'PHP'
<?php
declare(strict_types=1);

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Symfony\Component\HttpKernel\KernelInterface;

final class FeatureContext implements Context
{
    public function __construct(private KernelInterface $kernel) {}

    /** @Given the app boots */
    public function theAppBoots(): void
    {
        assert($this->kernel instanceof KernelInterface);
    }
}
PHP
fi

# 5) Smoke-фіча (якщо ще нема)
if [ ! -f features/smoke.feature ]; then
cat > features/smoke.feature <<'FEAT'
Feature: Smoke
  Scenario: App boots
    Given the app boots
FEAT
fi

echo "Behat bootstrap done."
