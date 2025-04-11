import { ref, computed, onUnmounted } from 'vue'

export function useCountdown(targetDateString) {
  // format the date to use `new Date()` (replace the space with "T")
  const targetDate = new Date(targetDateString.replace(' ', 'T')).getTime()
  const timeLeft = ref(calculateTimeLeft())

  // format time to string (days + hours:minutes:seconds)
  const formattedTime = computed(() => {
    const { days, hours, minutes, seconds } = timeLeft.value

    if (days <= 0 && hours <= 0 && minutes <= 0 && seconds <= 0) {
      return "Период истёк"
    }

    // add leading zeroes (5 → "05")
    const pad = (num) => num.toString().padStart(2, '0')

    if (days > 0) {
      return `${days} дней ${pad(hours)}:${pad(minutes)}:${pad(seconds)}`
    } else {
      return `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`
    }
  })

  function calculateTimeLeft() {
    const now = Date.now()
    const difference = targetDate - now

    if (difference <= 0) {
      return { days: 0, hours: 0, minutes: 0, seconds: 0 }
    }

    return {
      days: Math.floor(difference / (1000 * 60 * 60 * 24)),
      hours: Math.floor((difference / (1000 * 60 * 60)) % 24),
      minutes: Math.floor((difference / (1000 * 60)) % 60),
      seconds: Math.floor((difference / 1000) % 60),
    }
  }

  const timer = setInterval(() => {
    const newTimeLeft = calculateTimeLeft()
    timeLeft.value = newTimeLeft
    
    if (newTimeLeft.days <= 0 && newTimeLeft.hours <= 0 && newTimeLeft.minutes <= 0 && newTimeLeft.seconds <= 0) {
      clearInterval(timer)
    }
  }, 1000)

  onUnmounted(() => clearInterval(timer))

  return { 
    timeLeft, // { days, hours, minutes, seconds }
    formattedTime, // formatted string
  }
}