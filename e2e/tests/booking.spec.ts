import { test, expect, type Page } from '@playwright/test';

async function selectFirstAvailableSlot(page: Page) {
  const nextDayButton = page.getByRole('button', { name: 'Следующий день' });
  const slotButton = page.locator('button').filter({ hasText: /^\d{2}:\d{2}$/ });
  const noSlotsMessage = page.getByText('Нет доступных слотов на выбранную дату');

  for (let i = 0; i < 14; i += 1) {
    await slotButton.or(noSlotsMessage).first().waitFor({ state: 'visible' });

    const count = await slotButton.count();
    if (count > 0) {
      await slotButton.first().click();
      return;
    }

    await nextDayButton.click();
  }

  throw new Error('No available slots found in the next 14 days');
}

test('guest can book a meeting and owner can view and cancel it', async ({ page }) => {
  const guestName = 'Иван Тестов';
  const guestEmail = 'ivan@test.example';

  await page.goto('/');

  await expect(page.getByRole('heading', { name: 'Типы событий' })).toBeVisible();

  const card = page.getByTestId('event-type-card').filter({ hasText: 'Консультация' });
  await expect(card).toBeVisible();
  await card.getByRole('link', { name: 'Забронировать' }).click();

  await expect(page.getByRole('heading', { name: 'Бронирование' })).toBeVisible();
  await expect(page.getByText('Консультация · 30 минут')).toBeVisible();

  await selectFirstAvailableSlot(page);

  await expect(page.getByLabel('Имя')).toBeVisible();
  await page.getByLabel('Имя').fill(guestName);
  await page.getByLabel('Email').fill(guestEmail);

  await page.locator('form').getByRole('button', { name: 'Забронировать' }).click();

  await expect(page.getByRole('heading', { name: 'Бронирование подтверждено' })).toBeVisible();
  await expect(page.getByText(guestName)).toBeVisible();
  await expect(page.getByText(guestEmail)).toBeVisible();
  await expect(page.getByText('Консультация')).toBeVisible();

  await page.goto('/admin/bookings');
  await expect(page.getByRole('heading', { name: 'Предстоящие бронирования' })).toBeVisible();

  const row = page.locator('tbody tr').filter({ hasText: guestName });
  await expect(row).toBeVisible();

  await row.getByRole('button', { name: 'Отменить' }).click();

  await expect(row).toHaveCount(0);
  await expect(page.getByText('Нет предстоящих бронирований')).toBeVisible();
});
