<?php

namespace Tests\Unit;

use App\Imports\AthletesImport;
use App\Models\Competition;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class AthletesImportGenderTest extends TestCase
{
    public function test_it_normalizes_supported_gender_values(): void
    {
        $competition = new Competition();
        $import = new AthletesImport($competition);

        $this->assertSame('M', $import->normalizeGender('男'));
        $this->assertSame('M', $import->normalizeGender('male'));
        $this->assertSame('M', $import->normalizeGender('M'));

        $this->assertSame('F', $import->normalizeGender('女'));
        $this->assertSame('F', $import->normalizeGender('female'));
        $this->assertSame('F', $import->normalizeGender('F'));
    }

    public function test_it_accepts_category_code_and_name(): void
    {
        $competition = new Competition();
        $competition->setRelation('categories', new Collection([
            (object) ['id' => 1, 'code' => 'A', 'name' => 'U12', 'name_secondary' => 'Youth'],
            (object) ['id' => 2, 'code' => 'B', 'name' => 'U15', 'name_secondary' => 'Junior'],
        ]));

        $import = new AthletesImport($competition);

        $this->assertSame('A', $import->normalizeCategory('A'));
        $this->assertSame('A', $import->normalizeCategory('U12'));
        $this->assertSame('A', $import->normalizeCategory('Youth'));
        $this->assertSame('B', $import->normalizeCategory('Junior'));
    }

    public function test_it_accepts_short_weight_code_without_gender_prefix(): void
    {
        $competition = new Competition();
        $import = new AthletesImport($competition);

        $this->assertSame('FW42-', $import->normalizeWeightCode('42-', 'F'));
        $this->assertSame('FW42+', $import->normalizeWeightCode('42+', 'F'));
        $this->assertSame('FW42', $import->normalizeWeightCode('42', 'F'));
        $this->assertSame('MW42-', $import->normalizeWeightCode('42-', 'M'));
        $this->assertSame('MW42-', $import->normalizeWeightCode('M42-', 'M'));
        $this->assertSame('FWULW', $import->normalizeWeightCode('ULW', 'F'));
        $this->assertSame('MWULW', $import->normalizeWeightCode('ULW', 'M'));
        $this->assertSame('FWULW', $import->normalizeWeightCode('OPEN', 'F'));
    }
}
