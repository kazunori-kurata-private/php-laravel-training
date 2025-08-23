<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static ADMIN()
 * @method static static MANAGER()
 * @method static static STAFF()
 */
final class RoleType extends Enum
{
    public const ADMIN = 1;
    public const MANAGER = 2;
    public const STAFF = 3;

    public function description(): string
    {
        return match ($this) {
            self::ADMIN => '管理者',
            self::MANAGER => 'マネージャー',
            self::STAFF => 'スタッフ',
        };
    }
}
