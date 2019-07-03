<?php

namespace Tests\Unit;

use App\Sortable;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SortableTest extends TestCase
{
    protected $sortable;

    protected function setUp() :void
    {
        parent::setUp();

        $this->sortable = new Sortable('http://localhost');

    }
    /** @test */
    function builds_a_url_with_sortable_data()
    {
        $this->assertSame(
            'http://localhost?order=name&direction=asc',
            $this->sortable->url('name')
        );
    }

    function builds_a_url_with_desc_order_if_the_current_column_matches_the_given_one_and_the_current_direction_is_asc()
    {
        $this->sortable->setCurrentOrder('name','desc');

        $this->assertSame(
            'http://localhost?order=name&direction=desc',
            $this->sortable->url('name')
        );
    }
    /** @test */
    function returns_a_css_class_to_indicate_the_column_is_sortable()
    {        
        $this->assertSame('link-sortable', $this->sortable->classes('name'));
    }

    /** @test */
    function returns_css_classes_to_indicate_the_column_is_sorted_in_ascendet_order()
    {
        $this->sortable->setCurrentOrder('name','asc');
        
        $this->assertSame('link-sortable link-sorted-up', $this->sortable->classes('name'));
    }

    /** @test */
    function returns_css_classes_to_indicate_the_column_is_sorted_in_descendet_order()
    {
        $this->sortable->setCurrentOrder('name','desc');
        
        $this->assertSame('link-sortable link-sorted-down', $this->sortable->classes('name'));
    }
}
