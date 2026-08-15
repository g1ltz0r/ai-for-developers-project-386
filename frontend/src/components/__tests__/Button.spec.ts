import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import Button from '../../components/ui/Button.vue'

describe('Button', () => {
  it('renders default slot content', () => {
    const wrapper = mount(Button, {
      slots: {
        default: 'Click me',
      },
    })
    expect(wrapper.text()).toContain('Click me')
  })

  it('renders as disabled when disabled prop is true', () => {
    const wrapper = mount(Button, {
      props: { disabled: true },
      slots: { default: 'Disabled' },
    })
    expect(wrapper.find('button').attributes('disabled')).toBeDefined()
  })
})
