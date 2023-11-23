<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('services.PPRAuthorGridCellProvider');

import('lib.pkp.classes.controllers.grid.GridColumn');
import('lib.pkp.controllers.grid.users.author.AuthorGridRow');

class PPRAuthorGridCellProviderTest extends PPRTestCase {

    public function test_getTemplateVarsFromRowColumn_should_return_author_institution_when_column_is_institution() {
        [$row, $column, $author] = $this->createRowAndColumn('institution');
        $author->method('getLocalizedAffiliation')->willReturn('InstitutionValue');

        $target = new PPRAuthorGridCellProvider();
        $result = $target->getTemplateVarsFromRowColumn($row, $column);

        $this->assertEquals('InstitutionValue', $result['label']);
    }

    public function test_getTemplateVarsFromRowColumn_should_return_author_category_when_column_is_category() {
        [$row, $column, $author] = $this->createRowAndColumn('category');
        $author->method('getData')->with('category')->willReturn('CategoryValue');

        $target = new PPRAuthorGridCellProvider();
        $result = $target->getTemplateVarsFromRowColumn($row, $column);

        $this->assertEquals('CategoryValue', $result['label']);
    }

    public function test_getTemplateVarsFromRowColumn_should_return_author_department_when_column_is_department() {
        [$row, $column, $author] = $this->createRowAndColumn('department');
        $author->method('getData')->with('department')->willReturn('DepartmentValue');

        $target = new PPRAuthorGridCellProvider();
        $result = $target->getTemplateVarsFromRowColumn($row, $column);

        $this->assertEquals('DepartmentValue', $result['label']);
    }

    private function createRowAndColumn($columnId) {
        $row = $this->createMock(AuthorGridRow::class);
        $author = $this->getTestUtil()->createAuthor($this->getRandomId(), 'FamilyName', 'GivenName');
        $row->expects($this->once())->method('getData')->willReturn($author);

        $column = $this->createMock(GridColumn::class);
        $column->expects($this->once())->method('getId')->willReturn($columnId);
        return [$row, $column, $author];
    }
}