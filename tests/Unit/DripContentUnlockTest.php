<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class DripContentUnlockTest extends TestCase
{
    private function simulateLockedLessonIds(array $orderedLessonIds, array $completedLessonIds): array
    {
        $maxCompletedIndex = -1;
        foreach ($orderedLessonIds as $index => $lessonId) {
            if (in_array($lessonId, $completedLessonIds)) {
                $maxCompletedIndex = $index;
            }
        }

        $locked = [];
        foreach ($orderedLessonIds as $index => $lessonId) {
            if (in_array($lessonId, $completedLessonIds)) {
                continue;
            }

            if ($maxCompletedIndex === -1) {
                if ($index !== 0) {
                    $locked[] = $lessonId;
                }
            } elseif ($index > $maxCompletedIndex + 1) {
                $locked[] = $lessonId;
            }
        }

        return $locked;
    }

    public function test_first_lesson_unlocked_for_new_student(): void
    {
        $ordered = [101, 102, 103, 201, 202];
        $locked = $this->simulateLockedLessonIds($ordered, []);

        $this->assertNotContains(101, $locked);
        $this->assertContains(102, $locked);
        $this->assertContains(201, $locked);
    }

    public function test_first_lesson_of_next_section_unlocked_after_section_one_completion(): void
    {
        $ordered = [101, 102, 103, 201, 202];
        $completed = [101, 102, 103];
        $locked = $this->simulateLockedLessonIds($ordered, $completed);

        $this->assertNotContains(201, $locked, 'First lesson in section 2 should unlock after section 1 completion');
        $this->assertContains(202, $locked);
    }

    public function test_next_lesson_unlocked_with_out_of_order_completion(): void
    {
        $ordered = [101, 102, 103, 104];
        $completed = [101, 103];
        $locked = $this->simulateLockedLessonIds($ordered, $completed);

        $this->assertNotContains(102, $locked);
        $this->assertNotContains(104, $locked);
    }
}
